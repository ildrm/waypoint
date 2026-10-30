<?php

namespace App\Domain;

use App\Models\AssessmentRun;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

final class ReuseValidator
{
    public function assert(AssessmentRun $destination, array $question, array $answer, array $provenance): void
    {
        validator($provenance, ['sourceRunId' => 'required|uuid', 'sourceQuestionId' => 'required|string|max:100', 'sourceRevision' => 'required|integer|min:1', 'confirmedAt' => 'required|date'])->validate();
        $source = AssessmentRun::findOrFail($provenance['sourceRunId']);
        app(RunAccess::class)->assert($source);
        abort_unless($source->revision === $provenance['sourceRevision'], 412, 'The source facts changed; confirm the current evidence again.');
        $q = current(array_filter($source->release->manifest['bank']['questions'], fn ($q) => $q['id'] === $provenance['sourceQuestionId']));
        $known = $source->answers[$provenance['sourceQuestionId']] ?? null;
        $max = $destination->release->manifest['settings']['PARAM-REUSE-MAX-AGE']['value'] ?? 30;
        $when = Carbon::parse($provenance['confirmedAt']);
        if (! $q || $q['semantic_key'] !== $question['semantic_key'] || ! $known || $known['state'] !== 'known' || $answer['state'] !== 'known' || ! Canonical::same($known['value'], $answer['value']) || $source->updated_at->lt(now()->subDays($max)) || $when->gt(now()->addMinutes(5)) || $when->lt(now()->subDays($max))) {
            throw ValidationException::withMessages(['provenance' => 'Reused facts need compatible meaning, live access and recent explicit confirmation.']);
        }
    }
}
