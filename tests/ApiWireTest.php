<?php

namespace Tests;

use App\Domain\ReportService;
use App\Domain\RunService;
use App\Domain\SourceCopy;
use App\Jobs\RenderExport;
use App\Models\AssessmentReport;
use App\Models\AssessmentRun;
use App\Models\ExportJob;
use App\Models\Idempotency;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;

final class ApiWireTest extends TestCase
{
    use RefreshDatabase;

    private function schema(array $data, string $name): void
    {
        $doc = json_decode(file_get_contents(base_path('docs/implementation/openapi.yaml')), true, 512, JSON_THROW_ON_ERROR);
        $rewrite = function ($v) use (&$rewrite) {
            if (is_array($v)) {
                return array_map($rewrite, $v);
            }

return is_string($v) ? str_replace('#/components/schemas/', '#/$defs/', $v) : $v;
        };
        $schema = ['$schema' => 'https://json-schema.org/draft/2020-12/schema', '$defs' => $rewrite($doc['components']['schemas']), '$ref' => '#/$defs/'.$name];
        $result = (new Validator)->validate(json_decode(json_encode($data)), json_decode(json_encode($schema)));
        $this->assertTrue($result->isValid(), 'Wire response violates '.$name.': '.json_encode($result->error() ? (new ErrorFormatter)->formatFlat($result->error()) : []));
    }

    public function test_canonical_batch_answer_contract_is_atomic_and_idempotent(): void
    {
        $flow = $this->flow();
        $body = ['flowId' => $flow->id, 'locale' => 'en'];
        $response = $this->postJson('/api/v1/runs', $body, ['Idempotency-Key' => 'canonical-create-key'])->assertCreated()->assertHeader('ETag', '"1"');
        $this->schema($response->json(), 'Run');
        $id = $response->json('id');
        $token = $response->headers->get('X-Recovery-Code');
        $this->assertSame(64, strlen($token));
        $this->assertStringNotContainsString($token, json_encode(Idempotency::first()->response));
        $this->postJson('/api/v1/runs', $body, ['Idempotency-Key' => 'canonical-create-key'])->assertCreated()->assertJsonPath('id', $id)->assertHeader('X-Recovery-Code', $token);
        $answers = ['answers' => [['questionId' => 'Q-SW-MOBILE-PLATFORMS', 'state' => 'known', 'value' => ['android']], ['questionId' => 'Q-SW-SURFACES', 'state' => 'known', 'value' => ['web', 'mobile']]]];
        $saved = $this->putJson('/api/v1/runs/'.$id.'/answers', $answers, ['If-Match' => '"1"', 'Idempotency-Key' => 'canonical-batch-key'])->assertOk()->assertHeader('ETag', '"2"');
        $this->schema($saved->json(), 'Run');
        $this->assertDatabaseCount('answer_revisions', 1);
        $this->putJson('/api/v1/runs/'.$id.'/answers', $answers, ['If-Match' => '"1"', 'Idempotency-Key' => 'canonical-batch-key'])->assertOk()->assertJsonPath('revision', 2);
        $invalid = ['answers' => [['questionId' => 'Q-SW-SURFACES', 'state' => 'known', 'value' => ['web']], ['questionId' => 'Q-SW-MOBILE-PLATFORMS', 'state' => 'known', 'value' => ['android']]]];
        $this->putJson('/api/v1/runs/'.$id.'/answers', $invalid, ['If-Match' => '"2"', 'Idempotency-Key' => 'invalid-atomic-key'])->assertStatus(422)->assertHeader('Content-Type', 'application/problem+json');
        $this->assertSame(['web', 'mobile'], AssessmentRun::findOrFail($id)->answers['Q-SW-SURFACES']['value']);
        $this->assertSame(2, AssessmentRun::findOrFail($id)->revision);
        $removed = $this->putJson('/api/v1/runs/'.$id.'/answers', ['answers' => [['questionId' => 'Q-SW-SURFACES', 'state' => 'known', 'value' => ['web']]]], ['If-Match' => '"2"', 'Idempotency-Key' => 'remove-mobile-key'])->assertOk()->assertJsonPath('revision', 3);
        $this->assertContains('Q-SW-MOBILE-PLATFORMS', $removed->json('inactiveQuestionIds'));
        $this->putJson('/api/v1/runs/'.$id.'/answers', $answers, ['Idempotency-Key' => 'missing-etag-key'])->assertStatus(428);
    }

    public function test_canonical_report_and_export_operations_authorize_every_poll_and_download(): void
    {
        Storage::fake('local');
        $flow = $this->flow();
        $r = $this->postJson('/api/v1/runs', ['flowId' => $flow->id, 'locale' => 'en'], ['Idempotency-Key' => 'operation-create-key'])->assertCreated();
        $id = $r->json('id');
        $reportOp = $this->postJson('/api/v1/runs/'.$id.'/reports', ['locale' => 'en', 'allowPartial' => true], ['If-Match' => '"1"', 'Idempotency-Key' => 'operation-report-key'])->assertAccepted();
        $this->schema($reportOp->json(), 'Operation');
        $poll = $this->getJson('/api/v1/operations/'.$reportOp->json('id'))->assertOk()->assertJsonPath('status', 'complete');
        $report = $this->getJson($poll->json('resultUrl'))->assertOk();
        $this->schema($report->json(), 'Report');
        $rid = $report->json('id');
        $estimate = $this->getJson('/api/v1/estimates/'.$rid)->assertOk();
        $this->schema($estimate->json(), 'Estimate');
        $exportOp = $this->postJson('/api/v1/reports/'.$rid.'/exports', ['format' => 'json'], ['Idempotency-Key' => 'operation-export-key'])->assertAccepted();
        $this->schema($exportOp->json(), 'Operation');
        $export = $this->getJson($exportOp->json('resultUrl'))->assertOk();
        $this->schema($export->json(), 'Export');
        $this->get($export->json('downloadUrl'))->assertOk();
        $this->withSession(['assessment_principal' => 'foreign-principal']);
        $this->getJson('/api/v1/operations/'.$exportOp->json('id'))->assertNotFound();
        $this->get($export->json('downloadUrl'))->assertNotFound();
    }

    public function test_explicit_reprice_creates_a_version_and_matching_project_context(): void
    {
        $flow = $this->flow();
        $run = $this->runFor($flow);
        $report = app(ReportService::class)->generate($run, 1, 'initial-version-key');
        $op = $this->postJson('/api/v1/estimates/'.$report->id.'/revisions', ['acknowledgeRevision' => true], ['If-Match' => '"1"', 'Idempotency-Key' => 'explicit-reprice-key'])->assertAccepted();
        $this->getJson($op->json('resultUrl'))->assertOk()->assertJsonPath('supersedesId', $report->id);
        $this->assertDatabaseCount('assessment_reports', 2);
        $this->assertSame(1, $report->fresh()->input_revision);
    }

    public function test_confirmed_reuse_checks_source_owner_revision_and_typed_value(): void
    {
        $f = $this->flow();
        $source = $this->runFor($f, ['Q-SW-SURFACES' => ['state' => 'known', 'value' => ['web']]]);
        $target = $this->runFor($f);
        $answer = ['questionId' => 'Q-SW-SURFACES', 'state' => 'known', 'value' => ['web'], 'provenance' => ['sourceRunId' => $source->id, 'sourceQuestionId' => 'Q-SW-SURFACES', 'sourceRevision' => 1, 'confirmedAt' => now()->toIso8601String()]];
        $this->putJson('/api/v1/runs/'.$target->id.'/answers', ['answers' => [$answer]], ['If-Match' => '"1"', 'Idempotency-Key' => 'confirmed-reuse-key'])->assertOk();
        $source->update(['revision' => 2]);
        $this->putJson('/api/v1/runs/'.$target->id.'/answers', ['answers' => [$answer]], ['If-Match' => '"2"', 'Idempotency-Key' => 'stale-reuse-key'])->assertStatus(412);
    }

    public function test_unchanged_answer_does_not_deactivate_confirmed_dependents(): void
    {
        $f = $this->flow();
        $r = $this->runFor($f, ['Q-SW-SURFACES' => ['state' => 'known', 'value' => ['web', 'mobile']], 'Q-SW-MOBILE-PLATFORMS' => ['state' => 'known', 'value' => ['android']]]);
        $saved = app(RunService::class)->answer($r, 'Q-SW-SURFACES', 'known', ['web', 'mobile'], 1, 'unchanged-answer-key', false);
        $this->assertSame('known', $saved->answers['Q-SW-MOBILE-PLATFORMS']['state']);
    }

    public function test_published_flow_remains_available_while_its_next_draft_is_edited(): void
    {
        $bank = $this->bank('software');
        $bank['name'] = 'Pinned public name';
        $f = $this->flow('software', $bank);
        $f->update(['name' => 'Unpublished draft name', 'status' => 'draft']);
        $this->get('/')->assertOk()->assertSee('Pinned public name')->assertDontSee('Unpublished draft name');
        $wire = $this->getJson('/api/v1/flows')->assertOk();
        $this->schema($wire->json()[0], 'Flow');
        $this->assertSame('Pinned public name', $wire->json('0.name'));
    }

    public function test_duplicate_public_start_scenario_and_reprice_do_not_create_extra_versions(): void
    {
        $f = $this->flow();
        $form = ['flow_id' => $f->id, 'idempotency_key' => 'web-start-repeat-key'];
        $one = $this->post('/runs', $form)->assertRedirect();
        $two = $this->post('/runs', $form)->assertRedirect();
        $this->assertSame($one->headers->get('Location'), $two->headers->get('Location'));
        $this->assertDatabaseCount('assessment_runs', 1);
        $r = AssessmentRun::firstOrFail();
        $report = app(ReportService::class)->generate($r, 1, 'web-report-repeat-key');
        foreach (range(1, 2) as $i) {
            $this->post('/reports/'.$report->id.'/scenarios', ['idempotency_key' => 'web-fork-repeat-key'])->assertRedirect();
        }$this->assertDatabaseCount('assessment_runs', 2);
        foreach (range(1, 2) as $i) {
            $this->post('/reports/'.$report->id.'/reprice', ['idempotency_key' => 'web-reprice-repeat-key'])->assertRedirect();
        }$this->assertDatabaseCount('assessment_runs', 3);
        $this->assertDatabaseCount('assessment_reports', 2);
    }

    public function test_html_worker_uses_pinned_ui_and_locale_instead_of_current_dictionary(): void
    {
        Storage::fake('local');
        $source = 'Waypoint assessment report';
        $key = SourceCopy::key($source);
        Translation::create(['scope' => 'ui', 'key' => $key, 'locale' => 'fa', 'source_text' => $source, 'source_hash' => hash('sha256', $source), 'approved_source_hash' => hash('sha256', $source), 'text' => 'CURRENT_FA_STUB', 'status' => 'approved', 'critical' => false]);
        $f = $this->flow('startup', null, ['ui_translations' => ['fa' => [$key => 'PINNED_FA_STUB']], 'source_preview' => false]);
        $r = $this->runFor($f); // Synthetic language stubs verify snapshot behavior, never linguistic approval.
        $report = AssessmentReport::create(['run_id' => $r->id, 'input_revision' => 1, 'fingerprint' => hash('sha256', 'pinned-worker-test'), 'snapshot' => ['release_id' => $r->release_id], 'output' => ['workflow' => 'WF-STARTUP', 'locale' => 'fa', 'flow' => 'Synthetic language test', 'summary' => 'Synthetic evidence only', 'versions' => ['source_preview' => false], 'answers' => [], 'sources' => [], 'unresolved' => []], 'status' => 'partial']);
        app()->setLocale('fa');
        $this->assertSame('CURRENT_FA_STUB', __($source));
        $job = ExportJob::create(['report_id' => $report->id, 'format' => 'html', 'status' => 'queued']);
        (new RenderExport($job->id))->handle();
        $html = Storage::disk('local')->get($job->fresh()->path);
        $this->assertStringContainsString('PINNED_FA_STUB',$html);
        $this->assertStringContainsString('lang="fa"',$html);
        $this->assertStringNotContainsString('CURRENT_FA_STUB',$html);
        $this->assertFalse(app()->bound('business.translation_manifest'));
    }
}
