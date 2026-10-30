<?php

namespace App\Domain;

use Illuminate\Support\Carbon;

final class ConstraintReview
{
    public function evaluate(array $answers, array $estimate): array
    {
        $value = fn ($id) => ($answers[$id]['state'] ?? null) === 'known' ? ($answers[$id]['value'] ?? null) : null;
        $findings = [];
        $budget = $value('Q-SW-BUDGET');
        $price = $estimate['price'];
        if (is_array($budget) && ($budget['hard_cap'] ?? null) === true) {
            if (($price['total'] ?? null) === null) {
                $findings[] = ['code' => 'unpriced_hard_budget', 'message' => 'The hard budget cannot be confirmed while required scope or rates remain unpriced.', 'sources' => ['Q-SW-BUDGET']];
            } elseif (! empty($budget['currency']) && $budget['currency'] !== ($price['currency'] ?? null)) {
                $findings[] = ['code' => 'budget_currency_unconfirmed', 'message' => 'Budget and estimate currencies must match; no exchange rate is assumed.', 'sources' => ['Q-SW-BUDGET']];
            } elseif (isset($budget['initial_max']) && bccomp($price['total'], $budget['initial_max'], 8) > 0) {
                $findings[] = ['code' => 'hard_budget_exceeded', 'message' => 'The configured scope exceeds the stated hard budget. Revise scope, budget or delivery terms.', 'sources' => ['Q-SW-BUDGET', 'Q-SW-CONFIRM']];
            }if (($budget['includes_tax'] ?? null) === true && ($price['tax_state'] ?? null) === 'excluded_unconfigured') {
                $findings[] = ['code' => 'tax_budget_unconfirmed', 'message' => 'The budget includes taxes whose applicability and amounts have not been configured. Specialist confirmation is required.', 'sources' => ['Q-SW-BUDGET', 'Q-SW-REGULATION']];
            }
        }
        $deadline = $value('Q-SW-DEADLINE');
        if (is_array($deadline) && ! empty($deadline['mandatory_date'])) {
            if (($estimate['schedule']['weeks'] ?? null) === null) {
                $findings[] = ['code' => 'date_capacity_unconfirmed', 'message' => 'A mandatory date requires complete work, dependency and capacity evidence before a calendar commitment.', 'sources' => ['Q-SW-DEADLINE', 'Q-SW-AVAILABILITY-TEAM']];
            } elseif (empty($deadline['timezone'])) {
                $findings[] = ['code' => 'date_timezone_unknown', 'message' => 'Confirm the deadline time zone before comparing the calendar plan.', 'sources' => ['Q-SW-DEADLINE']];
            } else {
                try {
                    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/D', $deadline['mandatory_date'])) {
                        throw new \InvalidArgumentException;
                    }$due = Carbon::createFromFormat('!Y-m-d', $deadline['mandatory_date'], $deadline['timezone']);
                    if (! $due || $due->format('Y-m-d') !== $deadline['mandatory_date']) {
                        throw new \InvalidArgumentException;
                    }$due->endOfDay();
                    $finish = now($deadline['timezone'])->addWeeks($estimate['schedule']['weeks']);
                    if ($finish->gt($due)) {
                        $findings[] = ['code' => 'mandatory_date_conflict', 'message' => 'Under the disclosed start and capacity assumptions, the configured work finishes after the mandatory date. Review scope, capacity and dependencies.', 'sources' => ['Q-SW-DEADLINE', 'Q-SW-AVAILABILITY-TEAM']];
                    }
                } catch (\Throwable) {
                    $findings[] = ['code' => 'date_invalid', 'message' => 'The mandatory date or its time zone needs clarification.', 'sources' => ['Q-SW-DEADLINE']];
                }
            }
        }

        return $findings;
    }
}
