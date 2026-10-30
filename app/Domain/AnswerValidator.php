<?php

namespace App\Domain;

use Illuminate\Validation\ValidationException;
use Opis\JsonSchema\Validator;

final class AnswerValidator
{
    public function normalize(mixed $value, array $schema): mixed
    {
        $types = (array) ($schema['type'] ?? []);
        if (($schema['type'] ?? null) === 'object') {
            $out = [];
            foreach ($schema['properties'] ?? [] as $name => $definition) {
                $out[$name] = $this->normalize(is_array($value) ? ($value[$name] ?? null) : null, $definition);
            }

return $out;
        }
        if (in_array('array', $types, true)) {
            if (is_string($value)) {
                $value = preg_split('/\r?\n/', trim($value));
            }if ($value === null || $value === '') {
                return in_array('null', $types, true) ? null : [];
            }

return is_array($value) ? array_values(array_map(fn ($v) => $this->normalize($v, $schema['items'] ?? ['type' => 'string']), array_filter($value, fn ($v) => $v !== ''))) : $value;
        }
        if ($value === null || $value === '') {
            return in_array('null', $types, true) || in_array(null, $schema['enum'] ?? [], true) ? null : $value;
        }
        if (in_array('boolean', $types, true) && in_array($value, ['1', '0', true, false], true)) {
            return $value === '1' || $value === true;
        }
        if (in_array('integer', $types, true) && is_string($value) && preg_match('/^-?\d+$/D', $value)) {
            return (int) $value;
        }
        if (in_array('number', $types, true) && is_string($value) && is_numeric($value)) {
            return (float) $value;
        }

        return is_string($value) ? trim($value) : $value;
    }

    public function validate(array $question, string $state, mixed $value, bool $fromForm = true): array
    {
        if (! in_array($state, ['known', 'unknown', 'not_decided', 'not_applicable'], true)) {
            throw ValidationException::withMessages(['state' => 'Choose an explicit answer state.']);
        }
        if ($state === 'not_applicable') {
            throw ValidationException::withMessages(['state' => 'Applicability is determined by the released flow.']);
        }
        if ($state !== 'known') {
            return ['state' => $state, 'value' => null, 'reason' => $state === 'unknown' ? 'Not known yet.' : 'Not decided yet.'];
        }
        if ($fromForm && $question['id'] === 'Q-SW-AVAILABILITY-TEAM' && is_array($value)) {
            $rows = app(CapacityInput::class)->normalizeRows($value['capacity'] ?? []);
            $legacy = is_string($value['capacity_legacy'] ?? null) ? array_values(array_filter(preg_split('/\r?\n/', $value['capacity_legacy']), fn ($line) => trim($line) !== '')) : [];
            unset($value['capacity_legacy']);
            $value['capacity'] = array_merge($rows ?? [], $legacy) ?: null;
        }if ($fromForm) {
            $value = $this->normalize($value, $question['schema']);
        }
        $data = json_decode(json_encode($value, JSON_THROW_ON_ERROR));
        $schema = json_decode(json_encode($question['schema'], JSON_THROW_ON_ERROR));
        $result = (new Validator)->validate($data, $schema);
        if (! $result->isValid()) {
            throw ValidationException::withMessages(['value' => 'Check the required fields,allowed values,units and numeric boundaries. Use “I do not know” for unresolved information.']);
        }
        if (is_array($value) && ! array_filter($value, fn ($v) => $v !== null && $v !== '' && $v !== [])) {
            throw ValidationException::withMessages(['value' => 'Use the explicit unknown state for a wholly unresolved record.']);
        }
        if (is_array($value) && array_is_list($value) && in_array('none', $value, true) && count($value) > 1) {
            throw ValidationException::withMessages(['value' => '“None” cannot be combined with another selection.']);
        }

        return ['state' => 'known', 'value' => $value];
    }
}
