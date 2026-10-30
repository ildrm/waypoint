<?php

namespace App\Domain;

use App\Models\Project;

final class ProjectAccess
{
    public function assert(Project $project): void
    {
        $principal = app(RunAccess::class)->principal();
        $owner = auth()->check() && $project->user_id === auth()->id();
        $anonymous = $project->user_id === null && hash_equals($project->principal_hash, $principal);
        $linked = app(RunAccess::class)->query()->where('project_id', $project->id)->whereNotIn('status', ['deleted', 'suspended', 'expired'])->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->exists();
        abort_unless($owner || $anonymous || $linked, 404);
    }
}
