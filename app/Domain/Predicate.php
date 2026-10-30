<?php

namespace App\Domain;

use Illuminate\Validation\ValidationException;

final class Predicate
{
    private int $nodes = 0;

    public function evaluate(array $tree, array $answers, array $context = []): ?bool
    {
        $this->nodes = 0;

        return $this->walk($tree, $answers, $context, 0);
    }

    private function walk(array $t, array $a, array $context, int $depth): ?bool
    {
        if (++$this->nodes > 200 || $depth > 12) {
            throw ValidationException::withMessages(['rules' => 'Rule safety limit exceeded.']);
        }
        if (count($t) !== 1) {
            throw ValidationException::withMessages(['rules' => 'Each expression must have exactly one operator.']);
        }
        $op = array_key_first($t);
        $v = $t[$op];
        if ($op === 'always') {
            if (! is_bool($v)) {
                throw ValidationException::withMessages(['rules' => 'Always must be a boolean.']);
            }

return $v;
        }if (! is_array($v) && $op !== 'conflict') {
            throw ValidationException::withMessages(['rules' => 'The operator needs a structured argument.']);
        }if (in_array($op, ['all', 'any'], true) && (! array_is_list($v) || ! count($v) || count($v) > 50 || array_filter($v, fn ($x) => ! is_array($x)))) {
            throw ValidationException::withMessages(['rules' => 'Logical operators need a bounded nonempty expression list.']);
        }
        if (in_array($op, ['all', 'any'], true)) {
            $values = array_map(fn ($x) => $this->walk($x, $a, $context, $depth + 1), $v);
            if ($op === 'all') {
                return in_array(false, $values, true) ? false : (in_array(null, $values, true) ? null : true);
            }

return in_array(true, $values, true) ? true : (in_array(null, $values, true) ? null : false);
        }
        if ($op === 'not') {
            $result = $this->walk($v, $a, $context, $depth + 1);

            return $result === null ? null : ! $result;
        }
        if ($op === 'context_eq' && (! isset($v['key']) || ! is_string($v['key']) || ! array_key_exists('value', $v))) {
            throw ValidationException::withMessages(['rules' => 'Context comparisons need a key and value.']);
        }if ($op === 'conflict' && (! is_string($v) || strlen($v) > 100)) {
            throw ValidationException::withMessages(['rules' => 'Conflict reference must be a stable ID.']);
        }if ($op === 'context_eq') {
            return array_key_exists($v['key'], $context) ? $context[$v['key']] === $v['value'] : null;
        }
        if ($op === 'conflict') {
            return $context[$v] ?? false;
        }
        if (! in_array($op, ['eq', 'ne', 'in', 'contains', 'gt', 'gte', 'lt', 'lte', 'is_known', 'is_unknown', 'is_applicable'], true)) {
            throw ValidationException::withMessages(['rules' => 'Unsupported rule operator.']);
        }
        if (! isset($v['question_id']) || ! is_string($v['question_id']) || strlen($v['question_id']) > 100) {
            throw ValidationException::withMessages(['rules' => 'A stable question reference is required.']);
        }if (isset($v['path']) && (! is_string($v['path']) || ! preg_match('/^[a-zA-Z0-9_.]{1,160}$/D', $v['path']))) {
            throw ValidationException::withMessages(['rules' => 'Invalid field path.']);
        }if (! in_array($op, ['is_known', 'is_unknown', 'is_applicable'], true) && ! array_key_exists('value', $v)) {
            throw ValidationException::withMessages(['rules' => 'Comparison value is required.']);
        }$answer = $a[$v['question_id']] ?? null;
        if ($op === 'is_applicable') {
            return $answer !== null && ($answer['state'] ?? '') !== 'inactive';
        }
        $known = $answer && ($answer['state'] ?? '') === 'known';
        if ($op === 'is_known') {
            return $known;
        }
        if ($op === 'is_unknown') {
            return ! $known;
        }
        if (! $known) {
            return ($answer['state'] ?? '') === 'inactive' ? false : null;
        }
        $value = $answer['value'];
        foreach (explode('.', $v['path'] ?? '') as $key) {
            if ($key !== '') {
                $value = is_array($value) && array_key_exists($key, $value) ? $value[$key] : null;
            }
        }
        if ($value === null) {
            return null;
        }$expected = $v['value'] ?? null;

        return match ($op) {
            'eq' => $value === $expected,'ne' => $value !== $expected,'contains' => is_array($value) && in_array($expected, $value, true),'in' => is_array($expected) && in_array($value, $expected, true),
            'gt','gte','lt','lte' => $this->compare($value, $expected, $op),default => null
        };
    }

    private function compare(mixed $a, mixed $b, string $op): ?bool
    {
        if (! is_numeric($a) || ! is_numeric($b)) {
            return null;
        }$c = bccomp((string) $a, (string) $b, 8);

        return match ($op) {
            'gt' => $c > 0,'gte' => $c >= 0,'lt' => $c < 0,'lte' => $c <= 0
        };
    }

    public function references(array $tree): array
    {
        $out = [];
        $visit = function ($x) use (&$visit, &$out) {
            if (! is_array($x)) {
                return;
            }if (isset($x['question_id'])) {
                $out[] = $x['question_id'];
            }foreach ($x as $v) {
                $visit($v);
            }
        };
        $visit($tree);

        return array_values(array_unique($out));
    }
}
