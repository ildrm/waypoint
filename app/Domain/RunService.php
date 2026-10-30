<?php

namespace App\Domain;

use App\Models\AssessmentReport;
use App\Models\AssessmentRun;
use App\Models\Flow;
use App\Models\FlowRelease;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class RunService
{
    public function __construct(private RunAccess $access, private FlowEngine $engine, private AnswerValidator $validator, private Idempotent $idempotent) {}

    public function start(Flow $flow, string $locale, ?string $projectId = null): array
    {
        return DB::transaction(function () use ($flow, $locale, $projectId) {

            $release = FlowRelease::findOrFail($flow->active_release_id);
            abort_if($flow->status === 'retired', 410);
            $preview = app()->environment(['local', 'testing']) && config('platform.source_preview');
            abort_unless($release->status === 'published' || ($preview && $release->status === 'preview'), 409);
            abort_unless(isset(config('platform.locales')[$locale]), 422);
            if (! $preview) {
                abort_unless(isset($release->manifest['translations'][$locale]), 409);
            }$project = $projectId ? Project::findOrFail($projectId) : Project::create(['user_id' => auth()->id(), 'principal_hash' => $this->access->principal(), 'name' => 'Assessment context']);
            app(ProjectAccess::class)->assert($project);
            $token = Str::random(64);
            $run = AssessmentRun::create(['project_id' => $project->id, 'flow_id' => $flow->id, 'release_id' => $release->id, 'user_id' => auth()->id(), 'principal_hash' => $this->access->principal(), 'resume_token_hash' => hash('sha256', $token), 'locale' => $locale, 'answers' => [], 'last_activity_at' => now(), 'expires_at' => auth()->check() ? null : now()->addDays(config('platform.anonymous_retention_days'))]);
            Audit::event('flow_started', $run);

            return [$run->refresh(), $token];

        });
    }

    public function answer(AssessmentRun $run, string $questionId, string $state, mixed $value, int $revision, string $key, bool $fromForm = true): AssessmentRun
    {
        $this->access->assert($run);
        $payload = compact('questionId', 'state', 'value', 'revision');
        $result = $this->idempotent->execute('answer:'.$run->id, $key, $payload, function () use ($run, $questionId, $state, $value, $revision, $fromForm) {
            $current = AssessmentRun::whereKey($run->id)->lockForUpdate()->firstOrFail();
            $this->access->assert($current);
            abort_unless($current->revision === $revision, 412, 'This answer set changed. Reload and compare before saving.');
            $bank = $current->release->manifest['bank'];
            $active = array_column($this->engine->active($bank, $current->answers, $current->release->manifest), null, 'id');
            abort_unless(isset($active[$questionId]), 422, 'This question is not currently applicable.');
            $answer = $this->validator->validate($active[$questionId], $state, $value, $fromForm);
            $answers = $current->answers;
            $previous = $answers[$questionId] ?? null;
            $answers[$questionId] = $answer;
            $changed = Canonical::same($previous, $answer) ? ['answers' => $answers, 'invalidated' => []] : $this->engine->invalidate($bank, $answers, $questionId, $current->release->manifest);
            $new = $revision + 1;
            $affected = AssessmentRun::whereKey($current->id)->where('revision', $revision)->update(['answers' => json_encode($changed['answers'], JSON_THROW_ON_ERROR), 'revision' => $new, 'status' => 'active', 'last_activity_at' => now()]);
            abort_unless($affected === 1, 412);
            DB::table('answer_revisions')->insert(['run_id' => $current->id, 'revision' => $new, 'question_id' => $questionId, 'state' => $answer['state'], 'value' => json_encode($answer['value']), 'invalidated' => json_encode($changed['invalidated']), 'created_at' => now(), 'updated_at' => now()]);
            Audit::event('answer_saved', $current, ['question_count' => count($changed['answers'])]);

            return ['run_id' => $current->id, 'revision' => $new];
        });

        return AssessmentRun::findOrFail($result['run_id']);
    }

    public function scenario(AssessmentReport $report): AssessmentRun
    {
        return DB::transaction(function () use ($report) {

            $this->access->assert($report->run);
            $source = $report->run;
            $token = Str::random(64);
            $run = AssessmentRun::create(['project_id' => $source->project_id, 'flow_id' => $source->flow_id, 'release_id' => $report->snapshot['release_id'], 'user_id' => $source->user_id, 'principal_hash' => $source->principal_hash, 'resume_token_hash' => hash('sha256', $token), 'locale' => session('locale', $source->locale), 'answers' => $report->snapshot['answers'], 'baseline_report_id' => $report->id, 'last_activity_at' => now(), 'expires_at' => $source->expires_at]);
            session()->flash('resume_token', $token);
            Audit::event('scenario_created', $run);

            return $run->refresh();

        });
    }

    public function resume(string $token): AssessmentRun
    {
        abort_unless(strlen($token) === 64, 422);
        $run = AssessmentRun::where('resume_token_hash', hash('sha256', $token))->firstOrFail();
        abort_if($run->user_id !== null && $run->user_id !== auth()->id(), 403);
        abort_if(in_array($run->status, ['deleted', 'suspended', 'expired'], true) || $run->expires_at?->isPast(), 410);
        $run->update(['principal_hash' => $this->access->principal(), 'last_activity_at' => now(), 'status' => 'active']);

        return $run;
    }
}
