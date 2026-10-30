<?php

namespace App\Domain;

use App\Models\AssessmentReport;
use App\Models\AssessmentRun;
use App\Models\ExportJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class PrivacyService
{
    public function erase(AssessmentRun $run, ?User $actor): void
    {
        DB::transaction(function () use ($run) {
            $pending = [$run->id];
            $seen = [];
            while ($pending) {
                $id = array_shift($pending);
                if (isset($seen[$id])) {
                    continue;
                }$seen[$id] = true;
                $target = AssessmentRun::whereKey($id)->lockForUpdate()->firstOrFail();
                $reports = AssessmentReport::where('run_id', $id)->pluck('id');
                foreach (AssessmentRun::whereIn('baseline_report_id', $reports)->get() as $child) {
                    if ($child->user_id === $run->user_id && hash_equals($child->principal_hash, $run->principal_hash)) {
                        $pending[] = $child->id;
                    }
                }foreach (ExportJob::whereIn('report_id', $reports)->get() as $export) {
                    Storage::disk('local')->delete($export->path ?? ('exports/'.$export->id.'.'.$export->format));
                    $export->delete();
                }DB::table('assessment_reports')->where('run_id', $id)->update(['snapshot' => json_encode(['erased' => true]), 'output' => json_encode(['erased' => true]), 'internal_trace' => null, 'status' => 'deleted']);
                DB::table('answer_revisions')->where('run_id', $id)->delete();
                DB::table('analytics_events')->where('run_id', $id)->delete();
                DB::table('support_requests')->where('run_id', $id)->update(['detail' => null]);
                $target->update(['answers' => [], 'status' => 'deleted', 'resume_token_hash' => null, 'principal_hash' => hash('sha256', Str::random(64)), 'user_id' => null]);
            }Audit::record('privacy.erased', $run->id, ['count' => count($seen)]);
        });
    }
}
