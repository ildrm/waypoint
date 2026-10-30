<?php

namespace App\Domain;

use Illuminate\Validation\ValidationException;

final class CapacityScheduler
{
    public function schedule(array $packages, array $capacity, int $reviewWeeks = 0, float $fteWeekHours = 40): array
    {
        if ($fteWeekHours <= 0 || $fteWeekHours > 60) {
            $this->fail('Invalid FTE reference week.');
        }if ($reviewWeeks < 0 || $reviewWeeks > 520) {
            $this->fail('Review lead time must be nonnegative and bounded.');
        }$people = [];
        foreach ($capacity as $p) {
            if (! isset($p['id'],$p['roles'],$p['hours_week']) || ! is_string($p['id']) || isset($people[$p['id']]) || ! is_array($p['roles']) || ! count($p['roles']) || ! is_numeric($p['hours_week']) || $p['hours_week'] <= 0 || $p['hours_week'] > 60) {
                $this->fail('Each distinct person needs explicit roles and combined weekly capacity.');
            }$people[$p['id']] = $p;
        }
        $empty = ['status' => 'manual_review', 'weeks' => null, 'allocations' => [], 'unique_headcount' => null, 'peak_concurrent' => null];
        if (! $people) {
            return $empty + ['unresolved' => ['Available role/person capacity is required.']];
        }
        // One calendar week is60 scheduling slots. Each person's declared capacity is spread uniformly within it.
        $busy = [];
        $done = [];
        $pending = $packages;
        $alloc = [];
        $ids = [];
        foreach ($packages as $p) {
            if (isset($ids[$p['id']])) {
                $this->fail('Duplicate package ID.');
            }$ids[$p['id']] = true;
        }
        while ($pending) {
            $advanced = false;
            foreach ($pending as $index => $package) {
                if (($package['lead_weeks'] ?? 0) < 0 || ($package['lead_weeks'] ?? 0) > 520) {
                    $this->fail('Package lead time must be nonnegative and bounded.');
                }$deps = $package['dependencies'] ?? [];
                if (array_diff($deps, array_keys($done))) {
                    continue;
                }$start = ($deps ? max(array_map(fn ($d) => $done[$d], $deps)) : 0) + (float) ($package['lead_weeks'] ?? 0) * 60;
                $finish = $start;
                foreach ($package['role_hours'] as $role => $hours) {
                    $remaining = (float) $hours;
                    if ($remaining < 0) {
                        $this->fail('Role effort cannot be negative.');
                    }$eligible = array_filter($people, fn ($p) => in_array($role, $p['roles'], true));
                    if ($remaining && ! $eligible) {
                        return $empty + ['unresolved' => ['Missing capacity for role '.$role]];
                    }$slot = (int) floor($start);
                    while ($remaining > 0.00000001) {
                        if ($slot >= 520 * 60) {
                            $this->fail('Schedule exceeds the10-year safety limit.');
                        }$week = intdiv($slot, 60) + 1;
                        $offset = $slot % 60;
                        foreach ($eligible as $person => $p) {
                            if ($week < ($p['start_week'] ?? 1) || $week > ($p['end_week'] ?? 520) || in_array($week, $p['holiday_weeks'] ?? [], true)) {
                                continue;
                            }$unit = (float) $p['hours_week'] / 60;
                            $used = $busy[$person][$slot] ?? 0;
                            $fraction = max(0, 1 - max(0, $start - $slot));
                            $available = max(0, min($unit - $used, $unit * $fraction));
                            $h = min($remaining, $available);
                            if ($h > 0) {
                                $busy[$person][$slot] = $used + $h;
                                $alloc[$person.'|'.$role.'|'.$week.'|'.$package['id']] = ($alloc[$person.'|'.$role.'|'.$week.'|'.$package['id']] ?? 0) + $h;
                                $remaining -= $h;
                                $finish = max($finish, $slot + max(max(0, $start - $slot), $used / $unit) + $h / $unit);
                                if ($remaining <= 0.00000001) {
                                    break;
                                }
                            }
                        }$slot++;
                    }
                }$done[$package['id']] = $finish;
                unset($pending[$index]);
                $advanced = true;
            }if (! $advanced) {
                $this->fail('Work packages contain a cycle or undefined predecessor.');
            }
        }
        $rows = [];
        $unique = [];
        $peak = [];
        foreach ($alloc as $key => $hours) {
            [$person,$role,$week,$package] = explode('|', $key);
            $rows[] = ['person' => $person, 'role' => $role, 'week' => (int) $week, 'hours' => round($hours, 6), 'fte' => round($hours / $fteWeekHours, 6), 'package' => $package];
            $unique[$person] = true;
            $peak[$week][$person] = true;
        }$weeks = (int) ceil(($done ? max($done) : 0) / 60 - 0.00000001) + $reviewWeeks;

        return ['status' => 'planning', 'weeks' => $weeks, 'range_weeks' => [$weeks, $weeks + max(1, (int) ceil($weeks * .25))], 'allocations' => $rows, 'unique_headcount' => count($unique), 'peak_concurrent' => $peak ? max(array_map('count', $peak)) : 0, 'fte_reference_hours' => $fteWeekHours, 'peak_interval' => 'calendar_week', 'start_assumption' => 'Planning starts at report generation; confirm actual availability dates.', 'assumptions' => ['Capacity is spread uniformly across the declared week.', 'Unspecified start/end weeks use weeks 1/520 as planning boundaries.', 'Unlisted holidays and lead times are assumed zero; confirm them before committing.', 'The planning range is an uncalibrated expert allowance.'], 'calendar' => 'Uniform declared weekly capacity;explicit FTE reference, holidays and lead times.', 'unresolved' => []];
    }

    private function fail(string $m): never
    {
        throw ValidationException::withMessages(['capacity' => $m]);
    }
}
