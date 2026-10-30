<?php

namespace App\Domain;

use App\Models\PriceBook;
use Illuminate\Validation\ValidationException;

final class ProjectEstimator
{
    public function __construct(private Predicate $predicates, private ExactMoney $money, private CapacityScheduler $scheduler) {}

    public function select(?string $currency, ?string $market = null): ?PriceBook
    {
        if (! $currency) {
            return null;
        }$q = PriceBook::where('status', 'approved')->where('currency', $currency)->where('effective_from', '<=', now())->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>', now()));
        if ($market) {
            $q->where('market', $market);
        }if (! app()->environment(['local', 'testing'])) {
            $q->where('synthetic', false);
        }$books = $q->orderByDesc('version')->get();
        if ($books->count() > 1) {
            $this->fail('price', 'Overlapping approved price books need commercial review.');
        }

return $books->first();
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    private function quantity(array $driver, array $answers): ?string
    {
        $answer = $answers[$driver['question_id']] ?? null;
        if (($answer['state'] ?? null) !== 'known') {
            return null;
        }$value = $answer['value'];
        foreach (explode('.', $driver['path'] ?? '') as $key) {
            if ($key !== '') {
                $value = is_array($value) && array_key_exists($key, $value) ? $value[$key] : null;
            }
        }if (($driver['aggregation'] ?? null) === 'count') {
            return is_array($value) ? (string) count($value) : null;
        }if (! is_int($value) && ! is_string($value)) {
            return null;
        }

return preg_match('/^\d{1,24}(?:\.\d{1,8})?$/D', (string) $value) ? (string) $value : null;
    }

    private function driver(array $driver): void
    {
        validator($driver, ['question_id' => 'required|string|max:100', 'path' => 'sometimes|string|regex:/^[a-zA-Z0-9_.]{1,160}$/', 'aggregation' => 'sometimes|in:count'])->validate();
        if (array_diff(array_keys($driver), ['question_id', 'path', 'aggregation'])) {
            $this->fail('quantity_from', 'Unsupported quantity driver field.');
        }
    }

    private function feeQuantity(array $fee, array $answers): ?string
    {
        if (isset($fee['quantity_from'])) {
            $qty = $this->quantity($fee['quantity_from'], $answers);
        } else {
            $qty = isset($fee['quantity']) ? (string) $fee['quantity'] : null;
        }if ($qty === null) {
            return null;
        }$qty = bccomp($qty, (string) ($fee['included_quantity'] ?? '0'), 8) > 0 ? bcsub($qty, (string) ($fee['included_quantity'] ?? '0'), 8) : '0';
        if (bccomp($qty, '0', 8) > 0 && isset($fee['minimum_quantity']) && bccomp($qty, $fee['minimum_quantity'], 8) < 0) {
            $qty = $fee['minimum_quantity'];
        }

return $qty;
    }

    public function estimate(array $answers, ?PriceBook $book, array $settings = []): array
    {
        $empty = ['effort' => ['status' => 'manual_review', 'total_hours' => null, 'work_packages' => []], 'price' => ['status' => 'incomplete', 'currency' => null, 'total' => null, 'lines' => [], 'recurring' => [], 'tax_state' => 'excluded_unconfigured', 'book_version' => null], 'schedule' => ['status' => 'manual_review', 'weeks' => null, 'allocations' => [], 'unique_headcount' => null, 'peak_concurrent' => null], 'unresolved' => ['No approved effort/rate book exists for this project context.'], 'internal' => []];
        if (! $book) {
            return $empty;
        }
        $packages = [];
        $trace = [];
        $indeterminate = [];
        foreach ($book->work_packages as $p) {
            if (($settings['pricing_eligibility'][$p['id']] ?? true) === false) {
                continue;
            }$hit = in_array($p['id'], $settings['pricing_pending'] ?? [], true) ? null : $this->predicates->evaluate($p['when'] ?? ['always' => true], $answers);
            $trace[] = ['rule_id' => $p['id'], 'matched' => $hit, 'sources' => $this->predicates->references($p['when'] ?? [])];
            if ($hit === null) {
                $indeterminate[] = $p['id'];
            }if ($hit !== true) {
                continue;
            }$p['role_hours'] ??= [];
            foreach ($p['role_work'] ?? [] as $role => $driver) {
                $qty = $this->quantity($driver['quantity_from'], $answers);
                if ($qty === null) {
                    $indeterminate[] = $p['id'].':'.$role;

                    continue;
                }if (str_contains($qty, '.') && strlen(rtrim(explode('.', $qty)[1], '0')) > 4) {
                    $this->fail('hours', 'Role quantity supports at most four fractional digits.');
                }$hours = bcadd((string) ($p['role_hours'][$role] ?? 0), bcmul($qty, $driver['hours_per_unit'], 8), 8);
                if (bccomp($hours, '1000000', 8) > 0) {
                    $indeterminate[] = $p['id'].':'.$role;

                    continue;
                }$p['role_hours'][$role] = $hours;
            }$packages[] = $p;
        }
        if (! $packages) {
            return $empty + ['trace' => $trace];
        }$included = array_column($packages, 'id');
        $lines = [];
        $hours = 0;
        $roleTotals = [];
        $range = ['low' => '0', 'base' => '0', 'high' => '0'];
        $rangeComplete = true;
        foreach ($packages as &$p) {
            foreach ($p['dependencies'] ?? [] as $dep) {
                if (! in_array($dep, $included, true)) {
                    $indeterminate[] = $p['id'].':predecessor:'.$dep;
                }
            }foreach ($p['role_hours'] as $role => $h) {
                $this->money->decimal((string) $h);
                $hours += $h;
                $roleTotals[$role] = ($roleTotals[$role] ?? 0) + $h;
                $sources = $this->predicates->references($p['when'] ?? []);
                if (isset($p['role_work'][$role])) {
                    $sources[] = $p['role_work'][$role]['quantity_from']['question_id'];
                }$lines[] = ['charge_key' => $p['id'].':'.$role, 'item_id' => $p['id'], 'description' => $p['name'] ?? $p['id'], 'role' => $role, 'quantity' => (string) $h, 'unit' => 'hours', 'contingency_eligible' => $p['contingency_eligible'] ?? true, 'discount_eligible' => $p['discount_eligible'] ?? true, 'sources' => array_values(array_unique($sources))];
                $r = $p['role_ranges'][$role] ?? null;
                if (! $r || bccomp($r['low'], (string) $h, 8) > 0 || bccomp($r['high'], (string) $h, 8) < 0) {
                    $rangeComplete = false;
                } else {
                    foreach (['low', 'high'] as $bound) {
                        $range[$bound] = bcadd($range[$bound], $r[$bound], 8);
                    }
                }$range['base'] = bcadd($range['base'], (string) $h, 8);
            }
            foreach ($p['fixed_lines'] ?? [] as $fee) {
                $qty = $this->feeQuantity($fee, $answers);
                if ($qty === null) {
                    $indeterminate[] = $fee['id'];

                    continue;
                }$sources = $this->predicates->references($p['when'] ?? []);
                if (isset($fee['quantity_from'])) {
                    $sources[] = $fee['quantity_from']['question_id'];
                }$line = ['charge_key' => 'fee:'.$fee['id'], 'item_id' => $fee['id'], 'description' => $fee['name'] ?? $fee['id'], 'role' => $fee['id'], 'quantity' => $qty, 'rate' => $fee['rate'], 'unit' => $fee['unit'], 'contingency_eligible' => $fee['contingency_eligible'] ?? ($p['contingency_eligible'] ?? true), 'discount_eligible' => $fee['discount_eligible'] ?? ($p['discount_eligible'] ?? true), 'sources' => array_values(array_unique($sources))];
                foreach (['minimum_amount', 'minimum_quantity', 'included_quantity'] as $key) {
                    if (isset($fee[$key])) {
                        $line[$key] = $fee[$key];
                    }
                }$lines[] = $line;
            }
        }unset($p);
        $price = $this->money->calculate($lines, $book->rates, $book->exponent, $settings['contingency'] ?? $book->contingency, $settings['discount'] ?? $book->discount);
        $price += ['currency' => $book->currency, 'book_version' => $book->version, 'synthetic' => $book->synthetic, 'scope' => 'configured_work_packages'];
        $unresolved = [];
        $recurring = [];
        foreach ($book->recurring ?? [] as $entry) {
            if (isset($entry['amount'])) {
                $entry['amount'] = $this->money->round($entry['amount'], $book->exponent);
                $entry['status'] = 'configured';
            } else {
                $qty = $this->feeQuantity($entry, $answers);
                $entry['amount'] = $qty === null ? null : $this->money->calculate([['charge_key' => 'recurring', 'quantity' => $qty, 'rate' => $entry['rate']] + array_intersect_key($entry, array_flip(['minimum_amount']))], [], $book->exponent)['total'];
                $entry['quantity'] = $qty;
                $entry['status'] = $qty === null ? 'incomplete' : 'configured';
                if ($qty === null) {
                    $unresolved[] = 'Recurring quantity evidence is unresolved.';
                }
            }$entry['currency'] = $book->currency;
            $recurring[] = $entry;
        }$price['recurring'] = $recurring;
        if ($indeterminate) {
            $price['status'] = 'incomplete';
            $price['total'] = null;
            $price['missing'] = array_values(array_unique(array_merge($price['missing'], $indeterminate)));
            $unresolved[] = 'Unresolved work conditions, quantities or predecessors: '.implode(', ', array_unique($indeterminate));
        }
        $capacity = $settings['capacity'] ?? [];
        $schedule = $indeterminate ? $empty['schedule'] : $this->scheduler->schedule($packages, $capacity, (int) ($settings['review_weeks'] ?? 0), (float) ($settings['week_hours'] ?? 40));
        if (($schedule['status'] ?? null) === 'manual_review') {
            $unresolved[] = 'Explicit role capacity and complete work dependencies are required for a calendar commitment.';
        }
        $internalLines = array_map(function ($line) {
            unset($line['rate'],$line['minimum_amount']);

            return $line;
        }, $lines);
        $cost = $book->internal_rates ? $this->money->calculate($internalLines, $book->internal_rates, $book->exponent) : null;
        $allowed = ['id', 'name', 'role_hours', 'role_work', 'role_ranges', 'fixed_lines', 'dependencies', 'lead_weeks', 'source_status', 'contingency_eligible', 'discount_eligible'];
        $publicPackages = array_map(fn ($p) => array_intersect_key($p, array_flip($allowed)), $packages);

        return ['effort' => ['status' => $indeterminate ? 'partial' : 'planning', 'total_hours' => $indeterminate ? null : $hours, 'known_hours' => $hours, 'work_packages' => $publicPackages, 'role_hours' => $roleTotals, 'range_hours' => $rangeComplete && ! $indeterminate ? $range : null, 'range_status' => $rangeComplete ? 'configured_expert_assumptions' : 'range_not_configured', 'coefficient_status' => $book->synthetic ? 'hypothetical documentation fixture' : 'administered expert assumptions'], 'price' => $price, 'schedule' => $schedule, 'unresolved' => $unresolved, 'trace' => $trace, 'internal' => ['cost' => $cost]];
    }

    private function validateFee(array $fee, bool $recurring = false): void
    {
        $allowed = ['id', 'name', 'description', 'quantity', 'quantity_from', 'rate', 'unit', 'included_quantity', 'minimum_quantity', 'minimum_amount', 'contingency_eligible', 'discount_eligible'];
        if ($recurring) {
            $allowed = array_merge($allowed, ['interval', 'currency', 'amount']);
        }if (array_diff(array_keys($fee), $allowed)) {
            $this->fail('fixed_lines', 'Unsupported fee field; private costs belong in internal_rates.');
        }if ($recurring) {
            validator($fee, ['interval' => 'required|in:day,week,month,year'])->validate();
        } else {
            validator($fee, ['id' => 'required|string|regex:/^[a-zA-Z0-9_.-]{1,100}$/', 'unit' => 'required|string|max:80'])->validate();
        }if (isset($fee['amount'])) {
            $this->money->decimal($fee['amount']);

            return;
        }if (isset($fee['quantity']) === isset($fee['quantity_from'])) {
            $this->fail('quantity', 'Specify one exact quantity or a declared answer driver.');
        }if (isset($fee['quantity'])) {
            if (! is_int($fee['quantity']) && ! is_string($fee['quantity'])) {
                $this->fail('quantity', 'Use an integer or exact decimal quantity.');
            }$this->money->decimal((string) $fee['quantity']);
        } else {
            if (! is_array($fee['quantity_from'])) {
                $this->fail('quantity_from', 'A quantity source must be a structured object.');
            }$this->driver($fee['quantity_from']);
        }$this->money->decimal($fee['rate'] ?? null);
        foreach (['included_quantity', 'minimum_quantity', 'minimum_amount'] as $key) {
            if (isset($fee[$key])) {
                $this->money->decimal($fee[$key]);
            }
        }foreach (['contingency_eligible', 'discount_eligible'] as $key) {
            if (isset($fee[$key]) && ! is_bool($fee[$key])) {
                $this->fail('eligibility', 'Adjustment eligibility must be boolean.');
            }
        }
    }

    public function validateBook(PriceBook $book): void
    {
        if (! preg_match('/^[A-Z]{3}$/D', $book->currency) || $book->exponent < 0 || $book->exponent > 6) {
            $this->fail('currency', 'Use an explicit ISO currency and supported exponent.');
        }foreach ($book->rates as $rate) {
            $this->money->decimal($rate);
        }foreach ($book->internal_rates ?? [] as $rate) {
            $this->money->decimal($rate);
        }if (count($book->work_packages) > 500) {
            $this->fail('work_packages', 'Use at most500 work packages.');
        }foreach ($book->recurring ?? [] as $fee) {
            if (! is_array($fee)) {
                $this->fail('recurring', 'Recurring entries must be structured objects.');
            }$this->validateFee($fee, true);
            if (isset($fee['currency']) && $fee['currency'] !== $book->currency) {
                $this->fail('currency', 'Recurring and one-time currency must be explicit and consistent.');
            }
        }
        $ids = [];
        $fees = [];
        foreach ($book->work_packages as $p) {
            if (! isset($p['id']) || ! is_string($p['id']) || ! preg_match('/^[a-zA-Z0-9_.-]{1,100}$/D', $p['id']) || isset($ids[$p['id']])) {
                $this->fail('work_packages', 'Each package needs a unique stable ID.');
            }$ids[$p['id']] = true;
            validator($p, ['role_hours' => 'sometimes|array|max:40', 'role_work' => 'sometimes|array|max:40', 'role_work.*' => 'array', 'role_work.*.quantity_from' => 'required|array', 'fixed_lines' => 'sometimes|array|max:500', 'fixed_lines.*' => 'array', 'dependencies' => 'sometimes|array|max:500', 'dependencies.*' => 'string|max:100', 'role_ranges' => 'sometimes|array|max:40', 'lead_weeks' => 'sometimes|numeric|min:0|max:520'])->validate();
            $this->predicates->evaluate($p['when'] ?? ['always' => true], []);
            $roles = $p['role_hours'] ?? [];
            if (! is_array($roles) || count($roles) > 40) {
                $this->fail('role_hours', 'Role hours must be a bounded map.');
            }foreach ($roles as $role => $h) {
                if (! isset($book->rates[$role]) || is_float($h) || ! is_numeric($h) || $h < 0 || $h > 1000000) {
                    $this->fail('rates', 'Every labor role needs an exact nonnegative effort and an approved rate.');
                }$this->money->decimal((string) $h);
            }foreach ($p['role_work'] ?? [] as $role => $work) {
                if (! isset($book->rates[$role],$work['quantity_from'],$work['hours_per_unit'])) {
                    $this->fail('role_work', 'A role driver needs a rate, quantity source and exact hours per unit.');
                }$this->driver($work['quantity_from']);
                if (! is_string($work['hours_per_unit']) || ! preg_match('/^\d+(?:\.\d{1,4})?$/D', $work['hours_per_unit'])) {
                    $this->fail('hours_per_unit', 'Use an exact coefficient with at most four fractional digits.');
                }
            }foreach ($p['role_ranges'] ?? [] as $role => $range) {
                $this->money->decimal($range['low'] ?? null);
                $this->money->decimal($range['high'] ?? null);
                if (bccomp($range['low'], $range['high'], 8) > 0) {
                    $this->fail('role_ranges', 'The effort range is reversed.');
                }
            }foreach ($p['fixed_lines'] ?? [] as $fee) {
                $this->validateFee($fee);
                if (isset($fees[$fee['id']])) {
                    $this->fail('fixed_lines', 'A shared fee ID must be defined once.');
                }$fees[$fee['id']] = true;
            }if (($p['lead_weeks'] ?? 0) < 0 || ($p['lead_weeks'] ?? 0) > 520) {
                $this->fail('lead_weeks', 'Lead weeks must be nonnegative and bounded.');
            }
        }
        $map = array_column($book->work_packages, null, 'id');
        $seen = [];
        $stack = [];
        $visit = function ($id) use (&$visit, &$seen, &$stack, $map) {
            if (isset($stack[$id]) || ! isset($map[$id])) {
                $this->fail('dependencies', 'Cyclic or undefined package predecessor.');
            }if (isset($seen[$id])) {
                return;
            }$stack[$id] = true;
            foreach ($map[$id]['dependencies'] ?? [] as $dep) {
                $visit($dep);
            }unset($stack[$id]);
            $seen[$id] = true;
        };
        foreach (array_keys($map) as $id) {
            $visit($id);
        }foreach ([$book->contingency, $book->discount] as $ratio) {
            if (bccomp($this->money->decimal($ratio),'1',8) > 0) {
                $this->fail('adjustments','Ratios must not exceed one.');
            }
        }
    }
}
