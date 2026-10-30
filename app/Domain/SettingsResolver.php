<?php

namespace App\Domain;

use App\Models\ParameterDefinition;
use App\Models\ParameterOverride;
use Illuminate\Validation\ValidationException;

final class SettingsResolver
{
    public function validate(ParameterDefinition $definition, mixed $value): void
    {
        if ($value === null) {
            if (! $definition->nullable) {
                throw ValidationException::withMessages(['value' => 'Null is not allowed for this parameter.']);
            }

return;
        }
        $valid = match ($definition->type) {
            'integer' => is_int($value),'decimal' => is_string($value) && preg_match('/^-?\d+(?:\.\d+)?$/D', $value),'boolean' => is_bool($value),'enum' => in_array($value, $definition->constraints['values'] ?? [], true),'string' => is_string($value),default => false
        };
        if (! $valid) {
            throw ValidationException::withMessages(['value' => 'The value must match the declared parameter type.']);
        }
        foreach (['min', 'max'] as $bound) {
            if (isset($definition->constraints[$bound]) && is_numeric($value)) {
                $cmp = bccomp((string) $value, (string) $definition->constraints[$bound], 8);
                if (($bound === 'min' && $cmp < 0) || ($bound === 'max' && $cmp > 0)) {
                    throw ValidationException::withMessages(['value' => 'Parameter value is outside the declared range.']);
                }
            }
        }
    }

    public function effective(string $flowId): array
    {
        $values = [];
        foreach (ParameterDefinition::all() as $d) {
            $o = ParameterOverride::where('flow_id', $flowId)->where('parameter_id', $d->id)->first();
            if ($o && ! $d->allow_override) {
                throw ValidationException::withMessages(['override' => 'This parameter cannot be overridden.']);
            }$value = $o ? $o->value : $d->value;
            $this->validate($d, $value);
            $values[$d->id] = ['value' => $value, 'source' => $o ? 'flow_override' : 'general_default', 'definition_revision' => $d->revision];
        }

return $values;
    }

    public function reset(string $flowId, string $parameter): void
    {
        ParameterOverride::where('flow_id', $flowId)->where('parameter_id', $parameter)->delete();
    }
}
