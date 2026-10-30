<?php

namespace App\Domain;

use App\Models\AnalyticsEvent;
use App\Models\AssessmentRun;
use App\Models\AuditEvent;
use Illuminate\Support\Str;

final class Audit
{
    public static function record(string $action, ?string $resource, array $metadata = []): void
    {
        AuditEvent::create(['actor_id' => auth()->id(), 'action' => $action, 'resource_id' => $resource, 'metadata' => array_intersect_key($metadata, array_flip(['version', 'hash', 'status', 'count', 'reason_code'])), 'correlation_id' => (string) Str::uuid()]);
    }

    public static function event(string $event, ?AssessmentRun $run = null, array $dimensions = []): void
    {
        AnalyticsEvent::create(['event' => $event, 'run_id' => $run?->id, 'flow_slug' => $run?->flow?->slug, 'locale' => $run?->locale, 'dimensions' => array_intersect_key($dimensions, array_flip(['status', 'error_code', 'question_count']))]);
    }
}
