<?php

namespace Tests;

use App\Domain\ConstraintReview;
use App\Domain\ExactMoney;
use App\Domain\ProjectEstimator;
use App\Domain\ReportBuilder;
use App\Domain\RunService;
use App\Models\PriceBook;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class EstimateAssuranceTest extends TestCase
{
    use RefreshDatabase;

    private function drivenBook(): PriceBook
    {
        $book = $this->book([[
            'id' => 'build', 'role_hours' => ['DEV' => 4],
            'role_work' => ['DEV' => ['quantity_from' => ['question_id' => 'FEATURES', 'aggregation' => 'count'], 'hours_per_unit' => '2']],
            'role_ranges' => ['DEV' => ['low' => '8', 'high' => '14']],
            'fixed_lines' => [['id' => 'FEE', 'unit' => 'items', 'quantity_from' => ['question_id' => 'ITEMS'], 'included_quantity' => '5', 'minimum_quantity' => '3', 'minimum_amount' => '40', 'rate' => '10']],
        ]]);
        $book->internal_rates = ['DEV' => '20', 'FEE' => '5'];
        $book->recurring = [['interval' => 'month', 'unit' => 'items', 'quantity_from' => ['question_id' => 'ITEMS'], 'rate' => '2']];

        return $book;
    }

    public function test_answer_driven_effort_inclusions_minimums_recurring_and_private_cost_reconcile(): void
    {
        $book = $this->drivenBook();
        $service = app(ProjectEstimator::class);
        $service->validateBook($book);
        $out = $service->estimate(['FEATURES' => ['state' => 'known', 'value' => ['a', 'b', 'c']], 'ITEMS' => ['state' => 'known', 'value' => 7]], $book, ['capacity' => [['id' => 'P1', 'roles' => ['DEV'], 'hours_week' => 40]]]);
        $this->assertEquals(10, $out['effort']['total_hours']);
        $this->assertSame(['low' => '8.00000000', 'base' => '10.00000000', 'high' => '14.00000000'], $out['effort']['range_hours']);
        $this->assertSame('640.00', $out['price']['total']);
        $this->assertSame('14.00', $out['price']['recurring'][0]['amount']);
        $this->assertSame('215.00', $out['internal']['cost']['total']);
        $this->assertSame('3', $out['price']['lines'][1]['quantity']);
        $this->assertSame('40.00', $out['price']['lines'][1]['amount']);
        $this->assertSame(1, $out['schedule']['weeks']);
        $this->assertSame('calendar_week', $out['schedule']['peak_interval']);
        $this->assertNotEmpty($out['schedule']['assumptions']);
        $included = $service->estimate(['FEATURES' => ['state' => 'known', 'value' => []], 'ITEMS' => ['state' => 'known', 'value' => 5]], $book);
        $this->assertSame('0.00', $included['price']['lines'][1]['amount']);
    }

    public function test_unresolved_quantities_and_required_inactive_predecessors_never_receive_total_or_calendar(): void
    {
        $out = app(ProjectEstimator::class)->estimate(['ITEMS' => ['state' => 'unknown', 'value' => null]], $this->drivenBook());
        $this->assertNull($out['price']['total']);
        $this->assertNull($out['effort']['total_hours']);
        $this->assertNull($out['schedule']['weeks']);
        $book = $this->book([['id' => 'prior', 'when' => ['always' => false], 'role_hours' => ['DEV' => 1]], ['id' => 'next', 'role_hours' => ['DEV' => 1], 'dependencies' => ['prior']]]);
        $out = app(ProjectEstimator::class)->estimate([], $book);
        $this->assertNull($out['price']['total']);
        $this->assertNull($out['schedule']['weeks']);
        $this->assertStringContainsString('predecessor', implode(' ', $out['unresolved']));
    }

    public function test_nondiscountable_contingency_is_excluded_from_discount_base(): void
    {
        $out = (new ExactMoney)->calculate([['charge_key' => 'a', 'quantity' => '1', 'rate' => '100', 'contingency_eligible' => true, 'discount_eligible' => false], ['charge_key' => 'b', 'quantity' => '1', 'rate' => '100', 'contingency_eligible' => false, 'discount_eligible' => true]], [], 2, '0.10', '0.10');
        $this->assertSame('10.00', $out['discount']);
        $this->assertSame('200.00', $out['total']);
    }

    public function test_hard_budget_and_mandatory_date_conflicts_are_manual_review_with_arithmetic_preserved(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 30)->startOfDay());
        $answers = ['Q-SW-BUDGET' => ['state' => 'known', 'value' => ['currency' => 'USD', 'initial_max' => '50', 'hard_cap' => true, 'includes_tax' => true]], 'Q-SW-DEADLINE' => ['state' => 'known', 'value' => ['mandatory_date' => '2026-10-31', 'timezone' => 'UTC']]];
        $run = $this->runFor($this->flow(), $answers);
        $book = $this->book([['id' => 'base', 'role_hours' => ['DEV' => 10]]]);
        $out = app(ReportBuilder::class)->build($run, $book)['public'];
        $this->assertSame('600.00', $out['price']['total']);
        $this->assertSame('manual_review', $out['status']);
        $this->assertContains('hard_budget_exceeded', array_column($out['hard_constraint_conflicts'], 'code'));
        $this->assertContains('date_capacity_unconfirmed', array_column($out['hard_constraint_conflicts'], 'code'));
        $estimate = ['price' => ['total' => '600', 'currency' => 'USD', 'tax_state' => 'excluded_unconfigured'], 'schedule' => ['weeks' => 2]];
        $findings = app(ConstraintReview::class)->evaluate($answers, $estimate);
        $this->assertContains('mandatory_date_conflict', array_column($findings, 'code'));
        $answers['Q-SW-DEADLINE']['value']['mandatory_date'] = '2026-02-31';
        $this->assertContains('date_invalid', array_column(app(ConstraintReview::class)->evaluate($answers, $estimate), 'code'));
    }

    public function test_unrated_eligible_alternative_does_not_sort_as_zero(): void
    {
        $catalog = [];
        foreach (['rated' => ['balanced' => 2], 'unrated' => []] as $id => $preferences) {
            $catalog[] = ['stable_id' => $id, 'name' => $id, 'description' => 'Synthetic catalog evidence', 'domain' => 'software', 'eligibility' => ['always' => true], 'preferences' => $preferences, 'deliverables' => [], 'dimensions' => [], 'version' => 1];
        }
        $run = $this->runFor($this->flow('match', null, ['catalog' => $catalog]), ['Q-MA-GOAL' => ['state' => 'known', 'value' => 'software'], 'Q-MA-PRIORITY' => ['state' => 'known', 'value' => 'balanced']]);
        $out = app(ReportBuilder::class)->build($run, null)['public'];
        $this->assertSame('manual_review', $out['matching']['status']);
        $this->assertSame(['rated', 'unrated'], array_column($out['matching']['eligible'], 'stable_id'));
        $this->assertStringContainsString('before ranking', implode(' ', $out['unresolved']));
    }

    public function test_startup_clarification_displays_exact_source_questions_and_selected_labels(): void
    {
        $bank = $this->bank('startup');
        $map = array_column($bank['questions'], null, 'id');
        $run = $this->runFor($this->flow('startup'), ['Q-ST-10' => ['state' => 'known', 'value' => 'A'], 'Q-ST-11' => ['state' => 'known', 'value' => 'D']]);
        $page = $this->get('/runs/'.$run->id.'?question=Q-ST-C07')->assertOk()->assertSee('Q-ST-C07')->assertDontSee('[show the exact questions and selected answers]');
        foreach (['Q-ST-10' => 'A', 'Q-ST-11' => 'D'] as $id => $value) {
            $labels = array_column($map[$id]['options'], 'label', 'value');
            $page->assertSee($map[$id]['wording'])->assertSee($labels[$value]);
        }
    }

    public function test_recovery_rebinds_anonymous_ownership_and_revocation_still_applies(): void
    {
        $this->withSession(['assessment_principal' => 'first-owner']);
        [$run,$token] = app(RunService::class)->start($this->flow(), 'en');
        $this->withSession(['assessment_principal' => 'second-owner']);
        $this->get('/runs/'.$run->id)->assertNotFound();
        $this->post('/resume', ['token' => $token])->assertRedirect('/runs/'.$run->id);
        $this->get('/runs/'.$run->id)->assertOk();
        $this->assertSame(hash('sha256', 'second-owner'), $run->fresh()->principal_hash);
        $run->update(['status' => 'suspended']);
        $this->post('/resume',['token' => $token])->assertStatus(410);
        $this->get('/up')->assertOk()->assertExactJson(['status' => 'up'])->assertHeader('Cache-Control','no-store, private');
    }
}
