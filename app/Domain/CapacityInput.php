<?php

namespace App\Domain;

use Illuminate\Validation\ValidationException;

final class CapacityInput
{
    public function normalizeRows(mixed $rows): ?array
    {
        if ($rows === null || $rows === '') {
            return null;
        }if (is_string($rows)) {
            return array_values(array_filter(preg_split('/\r?\n/', $rows), fn ($line) => trim($line) !== '')) ?: null;
        }if (! is_array($rows)) {
            throw ValidationException::withMessages(['capacity' => 'Use contributor rows or existing capacity notes.']);
        } $out = [];
        foreach ($rows as $row) {
            if (is_string($row)) {
                $out[] = $row;

                continue;
            }if (! is_array($row)) {
                continue;
            }if (! array_filter($row, fn ($v) => $v !== '' && $v !== null && $v !== [])) {
                continue;
            }$person = ['id' => trim((string) ($row['id'] ?? '')), 'roles' => array_values(array_filter(array_map('trim', explode(',', (string) ($row['roles'] ?? ''))))), 'hours_week' => is_numeric($row['hours_week'] ?? null) ? (float) $row['hours_week'] : null];
            foreach (['start_week', 'end_week'] as $key) {
                if (($row[$key] ?? '') !== '') {
                    $person[$key] = (int) $row[$key];
                }
            }$person['holiday_weeks'] = array_map('intval', array_filter(array_map('trim', explode(',', (string) ($row['holiday_weeks'] ?? ''))), fn ($v) => $v !== ''));
            $this->validate($person);
            $out[] = json_encode($person, JSON_THROW_ON_ERROR);
        }

return $out ?: null;
    }

    public function parse(?array $rows): array
    {
        $people = [];
        foreach ($rows ?? [] as $row) {
            if (! is_string($row)) {
                return [];
            }try {
                $p = json_decode($row, true, 16, JSON_THROW_ON_ERROR);
                if (! is_array($p)) {
                    return [];
                }$this->validate($p);
            } catch (\Throwable) {
                return [];
            }$people[] = $p;
        }

return $people;
    }

    private function validate(array $p): void
    {
        validator($p, ['id' => 'required|string|regex:/^[a-zA-Z0-9_.-]{1,80}$/', 'roles' => 'required|array|min:1|max:20', 'roles.*' => 'required|string|regex:/^[a-zA-Z0-9_.-]{1,80}$/', 'hours_week' => 'required|numeric|gt:0|max:60', 'start_week' => 'sometimes|integer|min:1|max:520', 'end_week' => 'sometimes|integer|min:1|max:520', 'holiday_weeks' => 'sometimes|array|max:520', 'holiday_weeks.*' => 'integer|min:1|max:520'])->validate();
        if (isset($p['start_week'],$p['end_week']) && $p['end_week'] < $p['start_week']) {
            throw ValidationException::withMessages(['capacity' => 'Availability must end after it begins.']);
        }
    }
}
