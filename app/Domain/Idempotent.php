<?php

namespace App\Domain;

use App\Models\Idempotency;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

final class Idempotent
{
    private function canonical(mixed $v): mixed
    {
        if (! is_array($v)) {
            return $v;
        }if (! array_is_list($v)) {
            ksort($v);
        }foreach ($v as &$value) {
            $value = $this->canonical($value);
        }

        return $v;
    }

    private function replay(Idempotency $existing, string $hash): array
    {
        abort_if($existing->expires_at->isPast(), 409, 'The idempotency key expired; use a new key.');
        abort_unless(hash_equals($existing->payload_hash, $hash), 409, 'This key was used with a different request.');
        abort_if($existing->response === null, 409, 'Operation still in progress.');

        return $existing->response;
    }

    public function execute(string $scope, string $key, array $payload, callable $operation): array
    {
        abort_if(strlen($key) < 8 || strlen($key) > 128, 422, 'A valid idempotency key is required.');
        $scope = hash('sha256', $scope);
        $kh = hash('sha256', $key);
        $ph = hash('sha256', json_encode($this->canonical($payload), JSON_THROW_ON_ERROR));
        try {
            return DB::transaction(function () use ($scope, $kh, $ph, $operation) {
                $existing = Idempotency::where('scope', $scope)->where('key_hash', $kh)->lockForUpdate()->first();
                if ($existing) {
                    return $this->replay($existing, $ph);
                }$record = Idempotency::create(['scope' => $scope, 'key_hash' => $kh, 'payload_hash' => $ph, 'expires_at' => now()->addDay()]);
                $response = $operation();
                $record->update(['response' => $response]);

                return $response;
            }, 3);
        } catch (QueryException $error) {
            $existing = Idempotency::where('scope', $scope)->where('key_hash', $kh)->first();
            if ($existing) {
                return $this->replay($existing, $ph);
            }throw $error;
        }
    }
}
