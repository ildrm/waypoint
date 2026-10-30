<?php

namespace Tests;

use App\Domain\CapacityScheduler;
use App\Domain\ExactMoney;
use App\Domain\LocaleFormat;
use App\Domain\Predicate;
use PHPUnit\Framework\TestCase;

final class CalculationTest extends TestCase
{
    public function test_documented_c01_exact_reconciliation(): void
    {
        $m = new ExactMoney;
        $lines = [];
        $rates = ['BA' => '60.00', 'UX' => '50.00', 'DEV' => '60.00', 'QA' => '45.00', 'OPS' => '45.00'];
        foreach (['BA' => 40, 'UX' => 40, 'DEV' => 160, 'QA' => 64, 'OPS' => 16] as $role => $hours) {
            $lines[] = ['charge_key' => $role, 'role' => $role, 'quantity' => (string) $hours];
        }$out = $m->calculate($lines, $rates, 2, '0.10', '0.05');
        $this->assertSame('17600.00', $out['base']);
        $this->assertSame('1760.00', $out['contingency']);
        $this->assertSame('968.00', $out['discount']);
        $this->assertSame('18392.00', $out['total']);
        $this->assertSame('excluded_unconfigured', $out['tax_state']);
    }

    public function test_line_rounding_differs_from_aggregation_and_handles_currency_exponents(): void
    {
        $m = new ExactMoney;
        $lines = [];
        for ($i = 0; $i < 100; $i++) {
            $lines[] = ['charge_key' => (string) $i, 'quantity' => '1', 'rate' => '0.005'];
        }$this->assertSame('1.00', $m->calculate($lines, [], 2)['total']);
        $this->assertSame('0.50', $m->multiply('100', '0.005', 2));
        $this->assertSame('1', $m->round('0.5', 0));
        $this->assertSame('0.001', $m->round('0.0005', 3));
        $this->assertSame('9007199254740993.01', $m->multiply('1', '9007199254740993.005', 2));
    }

    public function test_zero_rate_is_valid_and_missing_rate_is_incomplete(): void
    {
        $m = new ExactMoney;
        $lines = [['charge_key' => 'base', 'role' => 'DEV', 'quantity' => '10']];
        $this->assertSame('0.00', $m->calculate($lines, ['DEV' => '0'], 2)['total']);
        $this->assertNull($m->calculate($lines, [], 2)['total']);
    }

    public function test_dependency_scheduler_respects_combined_person_capacity_and_holidays(): void
    {
        $s = new CapacityScheduler;
        $out = $s->schedule([['id' => 'design', 'role_hours' => ['UX' => 40]], ['id' => 'build', 'role_hours' => ['DEV' => 40], 'dependencies' => ['design']]], [['id' => 'one', 'roles' => ['UX', 'DEV'], 'hours_week' => 40, 'holiday_weeks' => [2]]]);
        $this->assertSame(3, $out['weeks']);
        $this->assertSame(1, $out['unique_headcount']);
        $this->assertSame(1, $out['peak_concurrent']);
        $used = [];
        foreach ($out['allocations'] as $a) {
            $used[$a['week']] = ($used[$a['week']] ?? 0) + $a['hours'];
        }foreach ($used as $h) {
            $this->assertLessThanOrEqual(40.000001, $h);
        }
    }

    public function test_capacity_missing_role_is_unknown_not_shorter_duration(): void
    {
        $out = (new CapacityScheduler)->schedule([['id' => 'x', 'role_hours' => ['QA' => 40]]], [['id' => 'dev', 'roles' => ['DEV'], 'hours_week' => 40]]);
        $this->assertNull($out['weeks']);
        $this->assertSame('manual_review', $out['status']);
    }

    public function test_three_valued_predicates_and_canonical_values(): void
    {
        $p = new Predicate;
        $expr = ['contains' => ['question_id' => 'Q', 'value' => 'mobile']];
        $this->assertNull($p->evaluate($expr, []));
        $this->assertTrue($p->evaluate($expr, ['Q' => ['state' => 'known', 'value' => ['web', 'mobile']]]));
        $this->assertFalse($p->evaluate($expr, ['Q' => ['state' => 'inactive', 'value' => ['mobile']]]));
        $this->assertFalse($p->evaluate(['all' => [['always' => false], $expr]], []));
    }

    public function test_locale_format_preserves_large_exact_decimal(): void
    {
        $this->assertSame('USD 9,007,199,254,740,993.01', LocaleFormat::money('9007199254740993.01', 'USD', 'en'));
        $this->assertStringContainsString('۹', LocaleFormat::number('9007199254740993.01', 'fa'));
    }

    public function test_adjustments_only_use_eligible_lines(): void
    {
        $out = (new ExactMoney)->calculate([['charge_key' => 'a', 'quantity' => '1', 'rate' => '100', 'contingency_eligible' => true, 'discount_eligible' => true], ['charge_key' => 'b', 'quantity' => '1', 'rate' => '100', 'contingency_eligible' => false, 'discount_eligible' => false]], [], 2, '0.10', '0.10');
        $this->assertSame('200.00', $out['base']);
        $this->assertSame('10.00',$out['contingency']);
        $this->assertSame('11.00',$out['discount']);
        $this->assertSame('199.00',$out['total']);
    }
}
