<?php

namespace Tests;

use App\Domain\AdminWorkspace;
use App\Domain\AnswerValidator;
use App\Domain\CapacityInput;
use App\Domain\CapacityScheduler;
use App\Domain\DemoFixtures;
use App\Domain\FlowEngine;
use App\Domain\Predicate;
use App\Domain\PrivacyService;
use App\Domain\ProjectEstimator;
use App\Domain\PublicationService;
use App\Domain\ReportBuilder;
use App\Domain\ReportService;
use App\Domain\RuleEffects;
use App\Domain\RunService;
use App\Domain\SchemaGuard;
use App\Domain\SettingsResolver;
use App\Domain\SourceCopy;
use App\Domain\TranslationManager;
use App\Jobs\RenderExport;
use App\Models\ExportJob;
use App\Models\FlowRelease;
use App\Models\ParameterDefinition;
use App\Models\ParameterOverride;
use App\Models\PriceBook;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_all123_documented_questions_validate_and_once_only_purchase_hides_retention(): void
    {
        $engine = app(FlowEngine::class);
        $total = 0;
        foreach (['startup', 'software', 'match'] as $slug) {
            $b = $this->bank($slug);
            $engine->validate($b);
            $total += count($b['questions']);
        }$this->assertSame(123, $total);
        $answers = ['Q-ST-09' => ['state' => 'known', 'value' => 'A'], 'Q-ST-C01' => ['state' => 'known', 'value' => 'C']];
        $active = array_column($engine->active($this->bank('startup'), $answers), 'id');
        $this->assertNotContains('Q-ST-23', $active);
    }

    public function test_public_journey_etags_duplicates_scenarios_and_foreign_access(): void
    {
        $flow = $this->flow();
        $this->get('/')->assertOk();
        $created = $this->postJson('/api/v1/runs', ['flow_id' => $flow->id], ['Idempotency-Key' => 'legacy-create-run-key'])->assertCreated();
        $id = $created->json('data.id');
        $payload = ['question_id' => 'Q-SW-SURFACES', 'state' => 'known', 'value' => ['web', 'mobile']];
        $this->postJson('/api/v1/runs/'.$id.'/answers', $payload, ['Idempotency-Key' => 'answer-key1'])->assertStatus(428);
        $this->postJson('/api/v1/runs/'.$id.'/answers', $payload, ['If-Match' => '"1"', 'Idempotency-Key' => 'answer-key1'])->assertOk()->assertHeader('ETag', '"2"');
        $this->postJson('/api/v1/runs/'.$id.'/answers', $payload, ['If-Match' => '"1"', 'Idempotency-Key' => 'answer-key1'])->assertOk();
        $this->postJson('/api/v1/runs/'.$id.'/answers', $payload + ['other' => 'ignored'], ['If-Match' => '"1"', 'Idempotency-Key' => 'answer-key2'])->assertStatus(412);
        $this->postJson('/api/v1/runs/'.$id.'/answers', array_replace($payload, ['value' => ['web']]), ['If-Match' => '"2"', 'Idempotency-Key' => 'answer-key1'])->assertStatus(409);
        $this->get('/runs/'.$id)->assertOk();
        $this->get('/runs/'.$id.'/review')->assertOk();
        $report = $this->postJson('/api/v1/runs/'.$id.'/reports', [], ['If-Match' => '"2"', 'Idempotency-Key' => 'report-key1'])->assertCreated();
        $rid = $report->json('data.id');
        $this->get('/reports/'.$rid)->assertOk();
        $this->get('/reports/'.$rid.'/export?format=json')->assertOk();
        $this->post('/reports/'.$rid.'/scenarios', ['idempotency_key' => 'web-scenario-test-key'])->assertRedirect();
        $this->assertDatabaseCount('assessment_runs', 2);
        $this->withSession(['assessment_principal' => 'foreign-owner']);
        $this->get('/runs/'.$id)->assertNotFound();
        $this->get('/reports/'.$rid.'/export?format=json')->assertNotFound();
    }

    public function test_upstream_change_removes_hidden_work_and_new_rate_does_not_mutate_history(): void
    {
        $flow = $this->flow();
        $run = $this->runFor($flow, ['Q-SW-SURFACES' => ['state' => 'known', 'value' => ['web', 'mobile']], 'Q-SW-DEVICE-FEATURES' => ['state' => 'known', 'value' => ['camera']], 'Q-SW-DEVICE-DETAILS' => ['state' => 'known', 'value' => ['permission' => 'needed']], 'Q-SW-BUDGET' => ['state' => 'known', 'value' => ['currency' => 'USD']]]);
        $package = ['id' => 'camera-integration', 'name' => 'Synthetic device integration', 'role_hours' => ['DEV' => 10], 'when' => ['contains' => ['question_id' => 'Q-SW-DEVICE-FEATURES', 'value' => 'camera']]];
        $book = $this->book([$package]);
        $service = app(ReportService::class);
        $old = $service->generate($run, 1, 'old-report-key');
        $this->assertSame('600.00', $old->output['price']['total']);
        $saved = app(RunService::class)->answer($run, 'Q-SW-SURFACES', 'known', ['web'], 1, 'change-surface-key', false);
        $this->assertSame('inactive', $saved->answers['Q-SW-DEVICE-DETAILS']['state']);
        $new = $service->generate($saved, 2, 'new-report-key');
        $this->assertNull($new->output['price']['total']);
        $this->assertSame('600.00', $old->fresh()->output['price']['total']);
        $scenario = app(RunService::class)->scenario($old);
        $book->update(['status' => 'retired']);
        $this->book([$package], '75.00', 2);
        $repriced = $service->generate($scenario, 1, 'repriced-report-key', $old);
        $this->assertSame('750.00', $repriced->output['price']['total']);
        $this->assertSame('600.00', $old->fresh()->output['price']['total']);
        $this->assertSame($book->id, $old->snapshot['price_book_id']);
        $this->assertArrayNotHasKey('internal_trace', $repriced->toArray());
        $this->assertArrayNotHasKey('internal_cost', $repriced->output['price']);
    }

    public function test_unknown_startup_evidence_has_actions_without_success_score(): void
    {
        $f = $this->flow('startup');
        $r = $this->runFor($f, ['Q-ST-15' => ['state' => 'known', 'value' => 'D'], 'Q-ST-22' => ['state' => 'unknown', 'value' => null]]);
        $out = app(ReportBuilder::class)->build($r, null)['public'];
        $this->assertSame('blocker', $out['priorities'][0]['severity']);
        $this->assertSame('partial', $out['status']);
        $this->assertArrayNotHasKey('score', $out);
        $this->assertArrayNotHasKey('success_probability', $out);
        $this->assertNull($out['price']);
    }

    public function test_matching_retains_equal_alternatives_and_rejects_unmet_prerequisites(): void
    {
        $catalog = [];
        foreach (['A', 'B'] as $id) {
            $catalog[] = ['stable_id' => $id, 'name' => $id, 'description' => 'Approved synthetic alternative', 'domain' => 'software', 'eligibility' => ['eq' => ['question_id' => 'Q-MA-GOAL', 'value' => 'software']], 'deliverables' => [], 'preferences' => ['balanced' => 1], 'dimensions' => [], 'version' => 1];
        }$f = $this->flow('match', null, ['catalog' => $catalog]);
        $r = $this->runFor($f, ['Q-MA-GOAL' => ['state' => 'known', 'value' => 'software']]);
        $out = app(ReportBuilder::class)->build($r, null)['public'];
        $this->assertCount(2, $out['matching']['eligible']);
        $r->update(['answers' => []]);
        $out = app(ReportBuilder::class)->build($r->fresh(), null)['public'];
        $this->assertCount(0, $out['matching']['eligible']);
    }

    public function test_general_and_flow_settings_keep_zero_false_and_null_distinct(): void
    {
        $f = $this->flow();
        $defs = [['zero', 'integer', 4, false], ['switch', 'boolean', true, false], ['nullable', 'string', 'default', true]];
        foreach ($defs as [$id,$type,$value,$nullable]) {
            ParameterDefinition::create(['id' => $id, 'name' => $id, 'type' => $type, 'unit' => 'test', 'value' => $value, 'constraints' => [], 'allow_override' => true, 'nullable' => $nullable]);
        }foreach (['zero' => 0, 'switch' => false, 'nullable' => null] as $id => $value) {
            ParameterOverride::create(['flow_id' => $f->id, 'parameter_id' => $id, 'value' => $value, 'reason' => 'test']);
        }$s = app(SettingsResolver::class);
        $v = $s->effective($f->id);
        $this->assertSame(0, $v['zero']['value']);
        $this->assertFalse($v['switch']['value']);
        $this->assertNull($v['nullable']['value']);
        $s->reset($f->id, 'switch');
        $this->assertTrue($s->effective($f->id)['switch']['value']);
        $this->assertSame('general_default', $s->effective($f->id)['switch']['source']);
    }

    public function test_translation_source_edit_marks_every_locale_stale_and_placeholders_are_checked(): void
    {
        $tm = app(TranslationManager::class);
        $tm->sync('fixture', ['hello' => 'Hello {name}']);
        $this->assertDatabaseCount('translations', 14);
        $t = Translation::where('scope', 'fixture')->where('locale', 'fa')->first();
        $t->update(['text' => 'سلام {name}']);
        $reviewer = $this->staff(['language_reviewer']);
        $tm->approve($t, $reviewer->id);
        $this->assertSame(1, $tm->coverage('fixture', ['hello' => 'Hello {name}'])['fa']['approved']);
        $tm->sync('fixture', ['hello' => 'Welcome {name}']);
        $this->assertSame('stale', $t->fresh()->status);
        $this->assertSame(0, $tm->coverage('fixture', ['hello' => 'Welcome {name}'])['fa']['approved']);
        $t->refresh()->update(['text' => 'سلام']);
        $this->expectException(ValidationException::class);
        $tm->approve($t, $reviewer->id);
    }

    public function test_publishing_requires_review_and_all14_current_critical_translations(): void
    {
        $f = $this->flow('match');
        $reviewer = $this->staff(['reviewer', 'publisher']);
        $this->actingAs($reviewer);
        $p = app(PublicationService::class);
        config(['platform.retention_approved' => true]);
        $p->review($f, $reviewer);
        $this->expectException(ValidationException::class);
        $p->publish($f->fresh(), $reviewer);
    }

    public function test_schema_network_references_and_executable_operators_are_rejected(): void
    {
        foreach ([['$ref' => 'https://evil.invalid/schema'], ['pattern' => '(a+)+$']] as $schema) {
            try {
                app(SchemaGuard::class)->validate($schema);
                $this->fail('Unsafe schema accepted.');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }$this->expectException(ValidationException::class);
        app(Predicate::class)->evaluate(['php' => 'system(...)'], []);
    }

    public function test_graph_cycles_and_undefined_question_references_are_rejected(): void
    {
        $b = $this->bank('match');
        $id = $b['questions'][0]['id'];
        $b['questions'][0]['applicability'] = ['eq' => ['question_id' => $id, 'value' => 'x']];
        $this->expectException(ValidationException::class);
        app(FlowEngine::class)->validate($b);
    }

    public function test_export_checks_live_revocation_and_retry_preserves_structured_report(): void
    {
        Storage::fake('local');
        $r = $this->runFor($this->flow('startup'));
        $report = app(ReportService::class)->generate($r, 1, 'export-report-key');
        $e = ExportJob::create(['report_id' => $report->id, 'format' => 'json', 'status' => 'queued']);
        (new RenderExport($e->id))->handle();
        $this->assertSame('ready', $e->fresh()->status);
        $r->update(['status' => 'suspended']);
        $this->get('/reports/'.$report->id.'/export?format=json')->assertForbidden();
        $other = ExportJob::create(['report_id' => $report->id, 'format' => 'html', 'status' => 'queued']);
        (new RenderExport($other->id))->handle();
        $this->assertSame('cancelled', $other->fresh()->status);
        $this->assertNotEmpty($report->fresh()->output);
    }

    public function test_verified_erasure_removes_answers_history_and_private_artifacts(): void
    {
        Storage::fake('local');
        $r = $this->runFor($this->flow('startup'), ['Q-ST-03' => ['state' => 'known', 'value' => 'A']]);
        $report = app(ReportService::class)->generate($r, 1, 'privacy-report-key');
        $e = ExportJob::create(['report_id' => $report->id, 'format' => 'json', 'status' => 'queued']);
        (new RenderExport($e->id))->handle();
        app(PrivacyService::class)->erase($r, $this->staff(['privacy_operator']));
        $this->assertSame([], $r->fresh()->answers);
        $this->assertSame(['erased' => true], $report->fresh()->output);
        $this->assertDatabaseCount('export_jobs', 0);
        $this->get('/reports/'.$report->id)->assertNotFound();
    }

    public function test_admin_authorization_and_typed_setting_changes_are_enforced_server_side(): void
    {
        $m = ParameterDefinition::create(['id' => 'test-param', 'name' => 'Fixture', 'type' => 'integer', 'unit' => 'items', 'value' => 3, 'constraints' => ['min' => 0, 'max' => 5], 'allow_override' => true]);
        $support = $this->staff(['support']);
        $this->actingAs($support);
        $this->postJson('/admin/manage', ['section' => 'general', 'operation' => 'save', 'id' => $m->id, 'revision' => 1, 'value_json' => '0'])->assertForbidden();
        $author = $this->staff(['content_author']);
        $this->actingAs($author)->withSession(['password_hash_web' => $author->getAuthPassword()]);
        $this->postJson('/admin/manage', ['section' => 'general', 'operation' => 'save', 'id' => $m->id, 'revision' => 1, 'value_json' => '0'])->assertOk();
        $this->assertSame(0, $m->fresh()->value);
        $this->postJson('/admin/manage', ['section' => 'general', 'operation' => 'save', 'id' => $m->id, 'revision' => 1, 'value_json' => '1'])->assertStatus(412);
    }

    public function test_issued_reports_and_released_manifests_are_immutable(): void
    {
        $r = $this->runFor($this->flow());
        $report = app(ReportService::class)->generate($r, 1, 'immutable-report-key');
        try {
            $report->update(['output' => []]);
            $this->fail('Report mutated.');
        } catch (\LogicException) {
            $this->assertNotEmpty($report->fresh()->output);
        }$this->expectException(\LogicException::class);
        $r->release->update(['manifest' => []]);
    }

    public function test_language_switch_keeps_answers_and_renders_rtl_source_preview_honestly(): void
    {
        $r = $this->runFor($this->flow(), ['Q-SW-SURFACES' => ['state' => 'known', 'value' => ['web']]]);
        $this->post('/locale', ['locale' => 'fa'])->assertRedirect();
        $this->get('/runs/'.$r->id)->assertOk()->assertSee('dir="rtl"', false)->assertSee('English', false);
        $this->assertSame(['web'], $r->fresh()->answers['Q-SW-SURFACES']['value']);
        $this->assertCount(14, config('platform.locales'));
    }

    public function test_all_six_software_example_known_values_pass_real_schema_validation(): void
    {
        $bank = array_column($this->bank('software')['questions'], null, 'id');
        $count = 0;
        foreach (glob(base_path('docs/16-examples/C0*.json')) as $file) {
            $fixture = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            foreach ($fixture['answers'] as $id => $answer) {
                if ($answer['state'] === 'known') {
                    $actual = app(AnswerValidator::class)->validate($bank[$id], 'known', $answer['value'], false);
                    $this->assertSame('known', $actual['state']);
                    $count++;
                }
            }
        }$this->assertGreaterThan(200, $count);
    }

    public function test_structured_capacity_preserves_one_person_multiple_roles(): void
    {
        $q = current(array_filter($this->bank('software')['questions'], fn ($q) => $q['id'] === 'Q-SW-AVAILABILITY-TEAM'));
        $value = ['capacity' => [['id' => 'P1', 'roles' => 'UX,DEV', 'hours_week' => '20', 'start_week' => '1', 'end_week' => '12', 'holiday_weeks' => '3']], 'timezone' => 'UTC', 'calendar' => 'Five days with4hours per day', 'seniority' => 'Senior', 'contracting' => 'Available'];
        $answer = app(AnswerValidator::class)->validate($q, 'known', $value);
        $people = app(CapacityInput::class)->parse($answer['value']['capacity']);
        $this->assertCount(1, $people);
        $this->assertSame(['UX', 'DEV'], $people[0]['roles']);
        $this->assertSame([3], $people[0]['holiday_weeks']);
        $schedule = app(CapacityScheduler::class)->schedule([['id' => 'UX', 'role_hours' => ['UX' => 20]], ['id' => 'DEV', 'role_hours' => ['DEV' => 20], 'dependencies' => ['UX']]], $people);
        $this->assertSame(2, $schedule['weeks']);
        $this->assertSame(1, $schedule['unique_headcount']);
    }

    public function test_admin_workspace_renders_every_authorized_section_and_support_landing(): void
    {
        $staff = $this->staff();
        $this->actingAs($staff);
        foreach (array_keys(AdminWorkspace::SECTIONS) as $section) {
            $this->get('/admin/workspace?section='.$section)->assertOk();
        }$support = $this->staff(['support']);
        $this->actingAs($support)->withSession(['password_hash_web' => $support->getAuthPassword()]);
        $this->get('/admin/workspace')->assertOk()->assertSee('Assessment sessions');
    }

    public function test_staff_mfa_enrollment_and_public_login_cannot_bypass_admin_mfa(): void
    {
        $staff = $this->staff();
        $staff->update(['app_authentication_secret' => null]);
        $this->actingAs($staff);
        $this->postJson('/admin/manage', ['section' => 'general', 'operation' => 'save'])->assertRedirect();
        $this->get('/admin/workspace')->assertRedirect();
        auth()->logout();
        $this->post('/login', ['email' => $staff->email, 'password' => 'synthetic-test-password'])->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_price_approval_requires_distinct_author_and_preserves_approved_book(): void
    {
        $author = $this->staff(['price_editor']);
        $reviewer = $this->staff(['price_editor', 'commercial_approver']);
        $book = $this->book([['id' => 'base', 'role_hours' => ['DEV' => 10]]]);
        $book->update(['status' => 'draft', 'created_by' => $author->id]);
        $this->actingAs($author);
        $this->postJson('/admin/manage', ['section' => 'prices', 'operation' => 'approve', 'id' => $book->id, 'revision' => $book->revision])->assertForbidden();
        $this->actingAs($reviewer)->withSession(['password_hash_web' => $reviewer->getAuthPassword()]);
        $this->postJson('/admin/manage', ['section' => 'prices', 'operation' => 'approve', 'id' => $book->id, 'revision' => $book->revision])->assertOk();
        $this->assertSame($reviewer->id, $book->fresh()->approved_by);
        $document = json_decode(app(AdminWorkspace::class)->editor('prices', $book), true);
        $document['rates']['DEV'] = '70.00';
        $this->postJson('/admin/manage', ['section' => 'prices', 'operation' => 'save', 'id' => $book->id, 'revision' => $book->revision, 'document' => json_encode($document)])->assertStatus(409);
        $this->assertSame('60.00', $book->fresh()->rates['DEV']);
    }

    public function test_password_change_revokes_existing_authenticated_session(): void
    {
        $user = $this->staff();
        $this->actingAs($user);
        $this->get('/admin/workspace')->assertOk();
        $user->update(['password' => 'rotated-synthetic-password']);
        $this->getJson('/admin/workspace')->assertUnauthorized();
    }

    public function test_snapshot_publication_changes_never_mutate_existing_release(): void
    {
        $f = $this->flow('match');
        $p = app(PublicationService::class);
        $old = $f->active_release_id;
        $bank = $f->bank;
        $bank['questions'][0]['wording'] = 'A separately released revised question?';
        $f->update(['bank' => $bank]);
        $new = $p->publish($f, null, true);
        $this->assertNotSame($old, $new->id);
        $this->assertNotSame($bank['questions'][0]['wording'], FlowRelease::findOrFail($old)->manifest['bank']['questions'][0]['wording']);
    }

    public function test_typed_rules_control_scope_eligibility_without_executing_code(): void
    {
        $rules = [['id' => 'scope', 'category' => 'scope', 'when' => ['always' => true], 'effect' => ['scope_item' => ['id' => 'feature-a', 'name' => 'A configured feature']]], ['id' => 'price', 'category' => 'pricing', 'when' => ['always' => true], 'effect' => ['pricing_eligibility' => ['item_id' => 'excluded', 'eligible' => false]]], ['id' => 'fact', 'category' => 'recommendation', 'when' => ['always' => true], 'effect' => ['derived_fact' => ['key' => 'decision.test', 'value' => false], 'recommendation' => ['id' => 'rec', 'text' => 'Review the configured evidence.']]]];
        $bank = $this->bank('match');
        app(FlowEngine::class)->validate($bank, $rules);
        $effects = app(RuleEffects::class)->run($rules, []);
        $this->assertFalse($effects['derived']['decision.test']['value']);
        $this->assertFalse($effects['pricing_eligibility']['excluded']);
        $this->assertSame('feature-a', $effects['scope'][0]['id']);
        $book = $this->book([['id' => 'included', 'role_hours' => ['DEV' => 1]], ['id' => 'excluded', 'role_hours' => ['DEV' => 10]]]);
        $out = app(ProjectEstimator::class)->estimate([], $book, ['pricing_eligibility' => $effects['pricing_eligibility']]);
        $this->assertSame('60.00', $out['price']['total']);
        $rules[0]['effect']['php'] = 'system(...)';
        $this->expectException(ValidationException::class);
        app(FlowEngine::class)->validate($bank, $rules);
    }

    public function test_released_business_settings_are_pinned_and_prod_gate_uses_synthetic_review_stubs(): void
    {
        config(['platform.retention_approved' => true]);
        $f = $this->flow('match');
        $param = ParameterDefinition::create(['id' => 'review-limit', 'name' => 'Test limit', 'type' => 'integer', 'unit' => 'items', 'value' => 3, 'constraints' => [], 'allow_override' => true]);
        $tm = app(TranslationManager::class);
        $tm->sync($f->id, $tm->sourceKeys($f->bank));
        $tm->sync('ui', SourceCopy::keys());
        $reviewer = $this->staff(['reviewer', 'publisher', 'language_reviewer']);
        $this->actingAs($reviewer); // Translation approval rows below are in-memory test doubles, never real linguistic review.
        DB::table('translations')->update(['text' => DB::raw('source_text'), 'status' => 'approved', 'approved_source_hash' => DB::raw('source_hash'), 'reviewed_by' => $reviewer->id, 'reviewed_at' => now()]);
        $publisher = app(PublicationService::class);
        $publisher->review($f, $reviewer);
        $release = $publisher->publish($f->fresh(), $reviewer);
        $this->assertSame('published', $release->status);
        $this->assertCount(14, $release->manifest['translations']);
        $this->assertSame(3, $release->manifest['settings']['review-limit']['value']);
        $param->update(['value' => 0, 'revision' => 2]);
        $this->assertSame(3, $release->fresh()->manifest['settings']['review-limit']['value']);
        $this->assertSame(0, app(SettingsResolver::class)->effective($f->id)['review-limit']['value']);
    }

    public function test_c01_demo_schedule_and_cost_reconcile_with_documented_partial_release_gate(): void
    {
        app(DemoFixtures::class)->load();
        $book = PriceBook::firstOrFail();
        $fixture = json_decode(file_get_contents(base_path('docs/16-examples/C01-informational-website.json')), true, 512, JSON_THROW_ON_ERROR);
        $people = [];
        foreach (['BA' => 40, 'UX' => 40, 'DEV' => 40, 'QA' => 40, 'OPS' => 40] as $role => $capacity) {
            $people[] = ['id' => $role.'1', 'roles' => [$role], 'hours_week' => $capacity];
        }$out = app(ProjectEstimator::class)->estimate($fixture['answers'], $book, ['capacity' => $people]);
        $this->assertSame('18392.00', $out['price']['total']);
        $this->assertSame(320, $out['effort']['total_hours']);
        $this->assertSame(8, $out['schedule']['weeks']);
        $this->assertSame(5, $out['schedule']['unique_headcount']);
        $this->assertSame(2, $out['schedule']['peak_concurrent']);
        $this->assertSame('10280.00', $out['internal']['cost']['total']);
    }

    public function test_public_locale_block_preserves_private_json_and_locale_recovery(): void
    {
        config(['platform.source_preview' => false]);
        $this->get('/')->assertStatus(409)->assertSee('Its content is not ready yet.');
        $this->post('/locale', ['locale' => 'fa'])->assertRedirect();
        $this->get('/')->assertStatus(409)->assertSee('dir="rtl"', false);
        $this->get('/admin/login')->assertOk();
    }
}
