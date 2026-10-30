<?php

namespace App\Domain;

use Illuminate\Validation\ValidationException;

final class RuleEffects
{
    public function validate(string $category, array $effect): void
    {
        $allowed = ['message', 'manual_review', 'derived_fact'];
        $specific = ['validation' => 'validation_code', 'recommendation' => 'recommendation', 'scope' => 'scope_item', 'classification' => 'classification', 'pricing' => 'pricing_eligibility', 'completion' => 'completion'];
        $allowed[] = $specific[$category] ?? '';
        if (array_diff(array_keys($effect), $allowed)) {
            $this->fail('Effect does not match its declared category.');
        }if (isset($effect['message']) && (! is_string($effect['message']) || strlen($effect['message']) > 2000)) {
            $this->fail('Invalid message.');
        }if (isset($effect['manual_review']) && ! is_bool($effect['manual_review'])) {
            $this->fail('Manual review must be a boolean.');
        }if (isset($effect['validation_code']) && ! is_string($effect['validation_code'])) {
            $this->fail('Invalid validation code.');
        }$schemas = ['derived_fact' => ['key' => 'required|string|regex:/^[a-zA-Z0-9_.-]{1,100}$/', 'value' => 'present'], 'recommendation' => ['id' => 'required|string|max:100', 'text' => 'required|string|max:2000'], 'scope_item' => ['id' => 'required|string|max:100', 'name' => 'required|string|max:2000', 'quantity' => 'nullable|numeric|min:0', 'unit' => 'nullable|string|max:100', 'surfaces' => 'sometimes|array|max:10', 'surfaces.*' => 'string|max:100'], 'classification' => ['dimension' => 'required|in:service_type,project_complexity,declared_current,declared_target', 'value' => 'required|string|max:200'], 'pricing_eligibility' => ['item_id' => 'required|string|max:100', 'eligible' => 'required|boolean'], 'completion' => ['allow_report' => 'required|boolean']];
        foreach ($schemas as $key => $rules) {
            if (isset($effect[$key])) {
                if (! is_array($effect[$key])) {
                    $this->fail('Effects must use structured fields.');
                }if (array_diff(array_keys($effect[$key]), array_keys(array_filter($rules, fn ($v, $k) => ! str_contains($k, '.'), ARRAY_FILTER_USE_BOTH)))) {
                    $this->fail('Unexpected effect property.');
                }validator($effect[$key], $rules)->validate();
            }
        }if (isset($effect['derived_fact']) && (is_array($effect['derived_fact']['value']) || is_object($effect['derived_fact']['value']))) {
            $this->fail('Derived constants must be scalar or null.');
        }
    }

    public function run(array $rules, array $answers, array $context = []): array
    {
        $out = ['trace' => [], 'derived' => [], 'scope' => [], 'recommendations' => [], 'classifications' => [], 'pricing_eligibility' => [], 'pricing_pending' => [], 'validation' => [], 'completion' => true];
        foreach ($rules as $rule) {
            $hit = app(Predicate::class)->evaluate($rule['when'], $answers, $context);
            $provenance = ['rule_id' => $rule['id'], 'sources' => app(Predicate::class)->references($rule['when'])];
            $out['trace'][] = $provenance + ['matched' => $hit];
            $e = $rule['effect'];
            if ($hit === null && isset($e['pricing_eligibility'])) {
                $out['pricing_pending'][] = $e['pricing_eligibility']['item_id'];
            }if ($hit !== true) {
                continue;
            }if (isset($e['derived_fact'])) {
                $out['derived'][$e['derived_fact']['key']] = ['value' => $e['derived_fact']['value']] + $provenance;
            }if (isset($e['scope_item'])) {
                $out['scope'][] = $e['scope_item'] + $provenance;
            }if (isset($e['recommendation'])) {
                $out['recommendations'][] = $e['recommendation'] + $provenance;
            }if (isset($e['classification'])) {
                $out['classifications'][] = $e['classification'] + ['kind' => 'declared_context_only'] + $provenance;
            }if (isset($e['validation_code'])) {
                $out['validation'][] = ['code' => $e['validation_code'], 'message' => $e['message'] ?? 'Clarify this validation finding.'] + $provenance;
            }if (isset($e['pricing_eligibility'])) {
                $id = $e['pricing_eligibility']['item_id'];
                $out['pricing_eligibility'][$id] = ($out['pricing_eligibility'][$id] ?? true) && $e['pricing_eligibility']['eligible'];
            }if (isset($e['completion'])) {
                $out['completion'] = $out['completion'] && $e['completion']['allow_report'];
            }
        }

return $out;
    }

    private function fail(string $m): never
    {
        throw ValidationException::withMessages(['rules' => $m]);
    }
}
