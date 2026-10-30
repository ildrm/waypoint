<?php

namespace App\Domain;

use Illuminate\Validation\ValidationException;

final class ExactMoney
{
    public function decimal(mixed $v): string
    {
        if (! is_string($v) || ! preg_match('/^\d{1,24}(?:\.\d{1,8})?$/D', $v)) {
            throw ValidationException::withMessages(['amount' => 'Use a nonnegative exact decimal string,not a floating-point amount.']);
        }

        return $v;
    }

    public function round(string $v, int $exponent): string
    {
        if ($exponent < 0 || $exponent > 6) {
            throw ValidationException::withMessages(['exponent' => 'Currency exponent must be0–6.']);
        }if (! preg_match('/^\d+(?:\.\d{1,16})?$/D', $v)) {
            throw ValidationException::withMessages(['amount' => 'Invalid computed decimal.']);
        }$half = $exponent === 0 ? '0.5' : '0.'.str_repeat('0', $exponent).'5';

        return bcadd($v, $half, $exponent);
    }

    public function multiply(string $quantity, string $rate, int $e): string
    {
        return $this->round(bcmul($this->decimal($quantity), $this->decimal($rate), 12), $e);
    }

    public function calculate(array $lines, array $rates, int $e, string $contingency = '0', string $discount = '0'): array
    {
        if (bccomp($this->decimal($contingency), '1', 8) > 0 || bccomp($this->decimal($discount), '1', 8) > 0) {
            throw ValidationException::withMessages(['adjustment' => 'Ratios must be0–1.']);
        }
        $base = bcadd('0', '0', $e);
        $eligibleContingency = $base;
        $eligibleDiscount = $base;
        $eligibleContingencyDiscount = $base;
        $priced = [];
        $seen = [];
        $missing = [];
        foreach ($lines as $line) {
            $key = $line['charge_key'];
            if (isset($seen[$key])) {
                throw ValidationException::withMessages(['lines' => 'Duplicate included work charge key.']);
            }$seen[$key] = true;
            $rate = $line['rate'] ?? ($rates[$line['role'] ?? ''] ?? null);
            if ($rate === null) {
                $missing[] = $key;

                continue;
            }$amount = $this->multiply((string) $line['quantity'], $rate, $e);
            if (isset($line['minimum_amount']) && bccomp((string) $line['quantity'], '0', 8) > 0) {
                $minimum = $this->round($this->decimal($line['minimum_amount']), $e);
                if (bccomp($amount, $minimum, $e) < 0) {
                    $amount = $minimum;
                }
            }
            $base = bcadd($base, $amount, $e);
            if ($line['contingency_eligible'] ?? true) {
                $eligibleContingency = bcadd($eligibleContingency, $amount, $e);
            }if ($line['discount_eligible'] ?? true) {
                $eligibleDiscount = bcadd($eligibleDiscount, $amount, $e);
                if ($line['contingency_eligible'] ?? true) {
                    $eligibleContingencyDiscount = bcadd($eligibleContingencyDiscount, $amount, $e);
                }
            }$priced[] = $line + ['rate' => $rate, 'amount' => $amount];
        }
        $c = $this->round(bcmul($eligibleContingency, $contingency, 12), $e);
        $discountableContingency = $this->round(bcmul($eligibleContingencyDiscount, $contingency, 12), $e);
        $adjusted = bcadd($eligibleDiscount, $discountableContingency, $e);
        $d = $this->round(bcmul($adjusted, $discount, 12), $e);
        $net = bcsub(bcadd($base, $c, $e), $d, $e);

        return ['status' => $missing ? 'incomplete' : 'complete', 'lines' => $priced, 'base' => $base, 'contingency' => $c, 'discount' => $d, 'total' => $missing ? null : $net, 'missing' => $missing, 'eligible_bases' => ['contingency' => $eligibleContingency, 'discount' => $adjusted], 'rounding' => 'half_up_per_line', 'tax_state' => 'excluded_unconfigured'];
    }
}
