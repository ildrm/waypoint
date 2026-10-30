<?php

namespace App\Domain;

use App\Models\Flow;

final class PublicFlows
{
    public function all(string $locale)
    {
        $preview = app()->environment(['local', 'testing']) && config('platform.source_preview');

        return Flow::where('status', '!=', 'retired')->whereHas('activeRelease', fn ($q) => $q->whereIn('status', $preview ? ['published', 'preview'] : ['published']))->with('activeRelease')->get()->each(function ($flow) use ($locale) {
            $m = $flow->activeRelease->manifest;
            $bank = $m['bank'];
            $flow->name = $m['translations'][$locale]['flow.name'] ?? $bank['name'] ?? $flow->name;
            $flow->description = $m['translations'][$locale]['flow.description'] ?? $bank['description'] ?? $flow->description;
            $flow->question_count = count($bank['questions']);
        });
    }
}
