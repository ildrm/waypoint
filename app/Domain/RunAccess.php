<?php

namespace App\Domain;

use App\Models\AssessmentRun;
use Illuminate\Support\Str;

final class RunAccess
{
    public function principal(): string
    {
        if (! session()->has('assessment_principal')) {
            session()->put('assessment_principal', Str::random(64));
        }

return hash('sha256', session('assessment_principal'));
    }

    public function assert(AssessmentRun $run): void
    {
        if (auth()->check() && ! auth()->user()->enabled) {
            abort(403);
        }$owner = auth()->check() && $run->user_id === auth()->id();
        $anonymous = $run->user_id === null && hash_equals($run->principal_hash, $this->principal());
        abort_unless($owner || $anonymous, 404);
        abort_if(in_array($run->status, ['suspended', 'deleted'], true), 403);
        abort_if($run->expires_at?->isPast(), 410);
    }

    public function query()
    {
        return AssessmentRun::query()->where(function ($q) {
            $q->where(fn ($a) => $a->whereNull('user_id')->where('principal_hash', $this->principal()));
            if (auth()->check()) {
                $q->orWhere('user_id', auth()->id());
            }
        });
    }
}
