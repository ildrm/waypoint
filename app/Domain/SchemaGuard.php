<?php

namespace App\Domain;

use Illuminate\Validation\ValidationException;

final class SchemaGuard
{
    public function validate(array $schema, int $depth = 0, int &$nodes = 0): void
    {
        if ($depth > 12 || ++$nodes > 2000) {
            $this->fail('Schema complexity limit exceeded.');
        }foreach ($schema as $key => $value) {
            if (in_array($key, ['patternProperties', 'propertyNames', '$ref', '$dynamicRef', '$recursiveRef', '$id', 'contentSchema', 'contentMediaType', 'contentEncoding'], true)) {
                $this->fail('Schema references and content loading are not supported.');
            }if ($key === 'pattern' && (! is_string($value) || ! in_array($value, ['^[0-9]+(\\.[0-9]+)?$', '^[0-9]+(\\.[0-9]{1,4})?$', '^[A-Z]{3}$'], true))) {
                $this->fail('Only audited numeric/currency patterns are supported.');
            }if (in_array($key, ['maxLength', 'maxItems', 'maxProperties'], true) && (! is_int($value) || $value > 64000 || $value < 0)) {
                $this->fail('Schema bounds exceed supported limits.');
            }if (is_array($value)) {
                $this->validate($value, $depth + 1, $nodes);
            }
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['schema' => $message]);
    }
}
