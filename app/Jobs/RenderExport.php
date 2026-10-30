<?php

namespace App\Jobs;

use App\Models\AssessmentRun;
use App\Models\ExportJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class RenderExport implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public string $exportId) {}

    public function backoff(): array
    {
        return [10, 60, 300];
    }

    private function html(ExportJob $job): string
    {
        $previous = app()->getLocale();
        app()->instance('business.translation_manifest', $job->report->run->release->manifest);
        app()->setLocale($job->report->output['locale'] ?? $job->report->run->locale);
        app('translator')->setLoaded([]);
        try {
            return view('reports.export', ['report' => $job->report])->render();
        } finally {
            app()->forgetInstance('business.translation_manifest');
            app()->setLocale($previous);
            app('translator')->setLoaded([]);
        }
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $candidate = ExportJob::with('report')->find($this->exportId);
            if (! $candidate) {
                return;
            }$run = AssessmentRun::whereKey($candidate->report->run_id)->lockForUpdate()->firstOrFail();
            $job = ExportJob::whereKey($this->exportId)->lockForUpdate()->first();
            if (! $job) {
                return;
            }if (in_array($run->status, ['deleted', 'suspended', 'expired'], true) || $run->expires_at?->isPast() || ($run->user_id && (! $run->user || ! $run->user->enabled))) {
                $job->update(['status' => 'cancelled', 'error_code' => 'access_revoked']);

                return;
            }if ($job->status === 'ready' && Storage::disk('local')->exists($job->path)) {
                return;
            }$job->increment('attempts');
            $job->update(['status' => 'rendering']);
            $path = 'exports/'.$job->id.'.'.$job->format;
            $content = $job->format === 'json' ? json_encode(['id' => $job->report->id, 'output' => $job->report->output], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : $this->html($job);
            Storage::disk('local')->put($path, $content);
            $job->update(['status' => 'ready', 'path' => $path, 'error_code' => null]);
        }, 3);
    }

    public function failed(?\Throwable $error): void
    {
        ExportJob::whereKey($this->exportId)->update(['status' => 'failed', 'error_code' => 'render_failed']);
    }
}
