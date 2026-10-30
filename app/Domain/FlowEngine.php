<?php

namespace App\Domain;

use Illuminate\Validation\ValidationException;

final class FlowEngine
{
    public function __construct(private Predicate $predicates) {}

    public function ordered(array $bank): array
    {
        $questions = $bank['questions'] ?? [];
        $map = array_column($questions, null, 'id');
        $seen = [];
        $stack = [];
        $out = [];
        usort($questions, fn ($a, $b) => ($a['order'] ?? array_search($a['id'], array_keys($map))) <=> ($b['order'] ?? array_search($b['id'], array_keys($map))));
        $visit = function ($id) use (&$visit, &$seen, &$stack, &$out, $map) {
            if (isset($stack[$id])) {
                throw ValidationException::withMessages(['bank' => 'Circular question dependency.']);
            }if (isset($seen[$id])) {
                return;
            }if (! isset($map[$id])) {
                throw ValidationException::withMessages(['bank' => 'Undefined question dependency.']);
            }$stack[$id] = true;
            foreach ($this->predicates->references($map[$id]['applicability']) as $dep) {
                $visit($dep);
            }unset($stack[$id]);
            $seen[$id] = true;
            $out[] = $map[$id];
        };
        foreach ($questions as $q) {
            $visit($q['id']);
        }

return $out;
    }

    public function context(array $answers, array $manifest = []): array
    {
        return ['RULE-ST-CONFLICT' => count($this->conflicts($answers)) > 0, 'approved_education_catalog' => (bool) ($manifest['approved_education_catalog'] ?? false)];
    }

    public function active(array $bank, array $answers, array $manifest = []): array
    {
        $context = $this->context($answers, $manifest);

        return array_values(array_filter($this->ordered($bank), fn ($q) => $this->predicates->evaluate($q['applicability'], $answers, $context) === true));
    }

    public function pending(array $bank, array $answers, array $manifest = []): array
    {
        $context = $this->context($answers, $manifest);

        return array_values(array_filter($this->ordered($bank), fn ($q) => $this->predicates->evaluate($q['applicability'], $answers, $context) === null));
    }

    public function invalidate(array $bank, array $answers, string $changed, array $manifest = []): array
    {
        $affected = [$changed];
        $invalidated = [];
        do {
            $expanded = false;
            foreach ($bank['questions'] as $q) {
                $deps = $this->predicates->references($q['applicability']);
                if ($q['id'] === 'Q-ST-C07') {
                    $deps = array_merge($deps, ['Q-ST-10', 'Q-ST-11', 'Q-ST-17', 'Q-ST-18', 'Q-ST-21']);
                }if ($q['id'] !== $changed && ! in_array($q['id'], $affected, true) && array_intersect($deps, $affected)) {
                    $affected[] = $q['id'];
                    $expanded = true;
                    if (isset($answers[$q['id']])) {
                        $answers[$q['id']] = ['state' => 'unanswered', 'value' => null, 'reason' => 'Upstream answer changed;reconfirmation required.'];
                        $invalidated[] = $q['id'];
                    }
                }
            }
        } while ($expanded);
        foreach ($this->ordered($bank) as $q) {
            $state = $this->predicates->evaluate($q['applicability'], $answers, $this->context($answers, $manifest));
            if ($state === false && isset($answers[$q['id']]) && $answers[$q['id']]['state'] !== 'inactive') {
                $answers[$q['id']] = ['state' => 'inactive', 'value' => null, 'reason' => 'No longer applicable.'];
                $invalidated[] = $q['id'];
            }
        }

        return ['answers' => $answers, 'invalidated' => array_values(array_unique($invalidated))];
    }

    public function conflicts(array $a): array
    {
        $v = fn ($id) => ($a[$id]['state'] ?? null) === 'known' ? ($a[$id]['value'] ?? null) : null;
        $out = [];
        if ($v('Q-ST-10') === 'A' && in_array($v('Q-ST-11'), ['D', 'E'], true)) {
            $out[] = ['code' => 'paid_evidence', 'sources' => ['Q-ST-10', 'Q-ST-11'], 'message' => 'Normal-price payment and no delivered purchase evidence need clarification.'];
        }
        if ($v('Q-ST-18') === 'A' && $v('Q-ST-17') === 'D') {
            $out[] = ['code' => 'outcome_offering', 'sources' => ['Q-ST-18', 'Q-ST-17'], 'message' => 'Observed customer outcomes and idea-only offering need context clarification.'];
        }
        $r = $v('Q-SW-REQUESTS');
        if (is_array($r) && isset($r['read_percent'],$r['write_percent']) && bccomp(bcadd((string) $r['read_percent'], (string) $r['write_percent'], 4), '100', 4) !== 0) {
            $out[] = ['code' => 'workload_mix', 'sources' => ['Q-SW-REQUESTS'], 'message' => 'Read and write shares must total100% for the same operation mix.'];
        }
        if (is_array($r) && isset($r['peak_rps'],$r['typical_rps']) && $r['peak_rps'] < $r['typical_rps']) {
            $out[] = ['code' => 'workload_peak', 'sources' => ['Q-SW-REQUESTS'], 'message' => 'Peak request rate is below the typical rate. Clarify the window.'];
        }
        $b = $v('Q-SW-BUDGET');
        if (is_array($b) && isset($b['initial_min'],$b['initial_max']) && bccomp($b['initial_min'], $b['initial_max'], 6) > 0) {
            $out[] = ['code' => 'budget_range', 'sources' => ['Q-SW-BUDGET'], 'message' => 'Budget minimum exceeds maximum.'];
        }

        return $out;
    }

    public function validate(array $bank, array $rules = []): void
    {
        if (! isset($bank['questions']) || ! is_array($bank['questions']) || ! count($bank['questions']) || count($bank['questions']) > 500) {
            throw ValidationException::withMessages(['bank' => 'A bank needs1–500 questions.']);
        }
        $ids = [];
        $options = [];
        foreach ($bank['questions'] as $q) {
            foreach (['id', 'wording', 'help', 'schema', 'applicability', 'semantic_key', 'translation_keys', 'stage', 'purpose', 'input_type'] as $key) {
                if (! array_key_exists($key, $q)) {
                    throw ValidationException::withMessages(['bank' => 'A question is missing a required field.']);
                }
            }if (isset($ids[$q['id']])) {
                throw ValidationException::withMessages(['bank' => 'Question IDs must be unique.']);
            }if (! is_string($q['id']) || ! preg_match('/^[a-zA-Z0-9_.-]{1,100}$/D', $q['id']) || ! is_string($q['wording']) || ! is_string($q['help']) || ! is_array($q['schema']) || ! in_array($q['input_type'], ['select_one', 'select_many', 'record'], true) || ! isset($q['translation_keys']['wording'],$q['translation_keys']['help'])) {
                throw ValidationException::withMessages(['bank' => 'Invalid question type, stable ID, copy or translation keys.']);
            }$ids[$q['id']] = true;
            app(SchemaGuard::class)->validate($q['schema']);
            foreach ($q['options'] ?? [] as $o) {
                if (isset($options[$o['id']])) {
                    throw ValidationException::withMessages(['bank' => 'Option IDs must be unique.']);
                }$options[$o['id']] = true;
            }if (($q['default'] ?? null) !== null) {
                throw ValidationException::withMessages(['bank' => 'Substantive defaults are prohibited.']);
            }$this->predicates->evaluate($q['applicability'], []);
        }
        $this->ordered($bank);
        $ruleIds = [];
        foreach ($rules as $rule) {
            if (isset($rule['id']) && isset($ruleIds[$rule['id']])) {
                throw ValidationException::withMessages(['rules' => 'Rule IDs must be unique.']);
            }if (isset($rule['id'])) {
                $ruleIds[$rule['id']] = true;
            }if (! isset($rule['id'],$rule['category'],$rule['when'],$rule['effect']) || ! in_array($rule['category'], ['validation', 'recommendation', 'scope', 'classification', 'pricing', 'completion'], true)) {
                throw ValidationException::withMessages(['rules' => 'Invalid rule contract/category.']);
            }$this->predicates->evaluate($rule['when'], []);
            foreach ($this->predicates->references($rule['when']) as $id) {
                if (! isset($ids[$id])) {
                    throw ValidationException::withMessages(['rules' => 'Undefined rule question reference.']);
                }
            }if (count($rules) > 500) {
                throw ValidationException::withMessages(['rules' => 'Too many rules.']);
            }if (! is_array($rule['effect'])) {
                throw ValidationException::withMessages(['rules' => 'Effects must be structured.']);
            }app(RuleEffects::class)->validate($rule['category'],$rule['effect']);
        }
    }
}
