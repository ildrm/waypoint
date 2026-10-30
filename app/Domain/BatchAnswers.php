<?php

namespace App\Domain;

use App\Models\AssessmentRun;
use Illuminate\Support\Facades\DB;

final class BatchAnswers
{
    public function save(AssessmentRun $run, array $items, int $revision, string $key): AssessmentRun
    {
        app(RunAccess::class)->assert($run);
        $result = app(Idempotent::class)->execute('batch:'.$run->id, $key, ['revision' => $revision, 'answers' => $items], function () use ($run, $items, $revision) {
            $current = AssessmentRun::whereKey($run->id)->lockForUpdate()->firstOrFail();
            app(RunAccess::class)->assert($current);
            abort_unless($current->revision === $revision, 412);
            $bank = $current->release->manifest['bank'];
            $map = [];
            foreach ($items as $item) {
                abort_if(isset($map[$item['questionId']]), 422, 'A question may appear only once in a batch.');
                $map[$item['questionId']] = $item;
            }$knownIds = array_column($bank['questions'], 'id');
            abort_if(array_diff(array_keys($map), $knownIds), 422);
            $answers = $current->answers;
            $invalidated = [];
            $history = [];
            foreach (app(FlowEngine::class)->ordered($bank) as $question) {
                $id = $question['id'];
                if (! isset($map[$id])) {
                    continue;
                }$item = $map[$id];
                $active = array_column(app(FlowEngine::class)->active($bank, $answers, $current->release->manifest), 'id');
                abort_unless(in_array($id, $active, true), 422, 'A supplied answer is not applicable in the revised branch.');
                $answer = app(AnswerValidator::class)->validate($question, $item['state'], $item['value'] ?? null, false);
                if (isset($item['provenance'])) {
                    app(ReuseValidator::class)->assert($current, $question, $answer, $item['provenance']);
                    $answer['provenance'] = $item['provenance'];
                }$previous = $answers[$id] ?? null;
                $answers[$id] = $answer;
                $changed = Canonical::same($previous, $answer) ? ['answers' => $answers, 'invalidated' => []] : app(FlowEngine::class)->invalidate($bank, $answers, $id, $current->release->manifest);
                $answers = $changed['answers'];
                $invalidated = array_merge($invalidated, $changed['invalidated']);
                $history[] = $id;
            }$current->update(['answers' => $answers, 'revision' => $revision + 1, 'status' => 'active', 'last_activity_at' => now()]);
            DB::table('answer_revisions')->insert(['run_id' => $current->id, 'revision' => $current->revision, 'question_id' => 'BATCH', 'state' => 'known', 'value' => json_encode(array_intersect_key($answers, $map), JSON_THROW_ON_ERROR), 'invalidated' => json_encode(array_unique($invalidated)), 'created_at' => now(), 'updated_at' => now()]);
            Audit::event('answer_saved', $current, ['question_count' => count($history)]);

            return ['run_id' => $current->id];
        });

        return AssessmentRun::findOrFail($result['run_id']);
    }
}
