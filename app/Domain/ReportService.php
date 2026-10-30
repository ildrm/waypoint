<?php

namespace App\Domain;

use App\Models\AssessmentReport;
use App\Models\AssessmentRun;

final class ReportService
{
    public function __construct(private RunAccess $access, private Idempotent $idempotent, private ProjectEstimator $estimator, private ReportBuilder $builder) {}

    public function generate(AssessmentRun $run, int $revision, string $key, ?AssessmentReport $baseline = null): AssessmentReport
    {
        $this->access->assert($run);
        if ($baseline) {
            $this->access->assert($baseline->run);
        }$result = $this->idempotent->execute('report:'.$run->id, $key, ['revision' => $revision, 'baseline' => $baseline?->id, 'locale' => session('locale', $run->locale)], function () use ($run, $revision, $baseline) {
            $run = AssessmentRun::whereKey($run->id)->lockForUpdate()->firstOrFail();
            $this->access->assert($run);
            abort_unless($run->revision === $revision, 412);
            $budget = $run->answers['Q-SW-BUDGET'] ?? null;
            $currency = ($budget['state'] ?? null) === 'known' ? ($budget['value']['currency'] ?? null) : null;
            $book = $this->estimator->select($currency);
            $fingerprint = hash('sha256', json_encode([$run->id, $run->revision, $book?->id, $baseline?->id, session('locale', $run->locale), '1.0']));
            $existing = AssessmentReport::where('fingerprint', $fingerprint)->first();
            if ($existing) {
                return ['report_id' => $existing->id];
            }$built = $this->builder->build($run, $book);
            $output = $built['public'];
            $out = $output['price'];
            $r = AssessmentReport::create(['run_id' => $run->id, 'input_revision' => $run->revision, 'fingerprint' => $fingerprint, 'baseline_id' => $baseline?->id ?? $run->baseline_report_id, 'price_book_id' => $book?->id, 'snapshot' => ['answers' => $run->answers, 'release_id' => $run->release_id, 'release_hash' => $run->release->hash, 'price_book_id' => $book?->id, 'price_book_version' => $book?->version, 'currency_exponent' => $book?->exponent, 'public_rates' => $book?->rates, 'method' => '1.0'], 'output' => $output, 'internal_trace' => $built['internal'], 'status' => $output['status'], 'valid_until' => $book ? now()->addDays($run->release->manifest['settings']['PARAM-ESTIMATE-VALIDITY']['value'] ?? $book->validity_days) : null]);
            $run->update(['status' => 'completed']);
            Audit::event('result_completed', $run, ['status' => $r->status]);

            return ['report_id' => $r->id];
        });

        return AssessmentReport::findOrFail($result['report_id']);
    }
}
