<?php

namespace App\Http\Controllers;

use App\Domain\AdminWorkspace;
use App\Domain\BatchAnswers;
use App\Domain\FlowEngine;
use App\Domain\Idempotent;
use App\Domain\PublicationService;
use App\Domain\PublicFlows;
use App\Domain\ReportService;
use App\Domain\RunAccess;
use App\Domain\RunService;
use App\Jobs\RenderExport;
use App\Models\AssessmentReport;
use App\Models\AssessmentRun;
use App\Models\ExportJob;
use App\Models\Flow;
use App\Models\Operation;
use App\Models\PriceBook;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class ApiWireController
{
    public function __construct(private RunAccess $access) {}

    private function strict(Request $r, array $allowed): void
    {
        abort_if(array_diff(array_keys($r->all()), $allowed), 422, 'Unexpected request fields.');
    }

    private function revision(Request $r): int
    {
        abort_unless($r->hasHeader('If-Match'), 428);
        abort_unless(preg_match('/^"([0-9]+)"$/D', $r->header('If-Match'), $m), 422);

        return (int) $m[1];
    }

    private function key(Request $r): string
    {
        $v = $r->header('Idempotency-Key');
        abort_unless(is_string($v) && strlen($v) >= 8 && strlen($v) <= 128, 422, 'An idempotency key of8–128 characters is required.');

        return $v;
    }

    private function run(AssessmentRun $run): array
    {
        $active = app(FlowEngine::class)->active($run->release->manifest['bank'], $run->answers, $run->release->manifest);
        $answers = [];
        $inactive = [];
        foreach ($run->answers as $id => $a) {
            if ($a['state'] === 'inactive') {
                $inactive[] = $id;

                continue;
            }if (! in_array($a['state'], ['known', 'unknown', 'not_decided', 'not_applicable'], true)) {
                continue;
            }$wire = ['questionId' => $id, 'state' => $a['state']];
            if ($a['state'] === 'known') {
                $wire['value'] = $a['value'];
            }if (isset($a['provenance'])) {
                $wire['provenance'] = $a['provenance'];
            }$answers[] = $wire;
        }$next = null;
        foreach ($active as $q) {
            if (! in_array($run->answers[$q['id']]['state'] ?? 'unanswered', ['known', 'unknown', 'not_decided'], true)) {
                $next = $q['id'];
                break;
            }
        }

return ['id' => $run->id, 'projectId' => $run->project_id, 'flowReleaseId' => $run->release_id, 'revision' => $run->revision, 'status' => $run->status, 'locale' => $run->locale, 'answers' => $answers, 'nextQuestionId' => $next, 'conflicts' => app(FlowEngine::class)->conflicts($run->answers), 'inactiveQuestionIds' => $inactive];
    }

    private function evidence(AssessmentReport $report, array $answerIds = [], array $ruleIds = []): array
    {
        return ['answerIds' => array_values(array_unique($answerIds)), 'ruleIds' => array_values(array_unique($ruleIds)), 'assumptionIds' => $report->price_book_id ? ['ASM-ADMINISTERED-COEFFICIENTS'] : []];
    }

    private function report(AssessmentReport $report): array
    {
        $o = $report->output;
        $outputs = [];
        $mapping = ['OUT-SCOPE' => 'scope', 'OUT-ARCH' => 'architecture', 'OUT-EFFORT' => 'effort', 'OUT-PRICE' => 'price', 'OUT-SCHEDULE' => 'schedule', 'OUT-TEAM' => 'schedule', 'OUT-ACTIONS' => 'priorities', 'OUT-MATCH' => 'matching'];
        foreach ($mapping as $field => $key) {
            if (isset($o[$key]) && $o[$key] !== []) {
                $outputs[] = ['field' => $field, 'summary' => is_string($o[$key]) ? $o[$key] : json_encode($o[$key], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), 'evidence' => $this->evidence($report, array_column($o['answers'] ?? [], 'question_id'))];
            }
        }$dto = ['id' => $report->id, 'runId' => $report->run_id, 'inputRevision' => $report->input_revision, 'locale' => $o['locale'] ?? $report->run->locale, 'status' => in_array($report->status, ['partial', 'manual_review'], true) ? $report->status : 'complete', 'outputs' => $outputs];
        if (isset($o['price'])) {
            $dto['estimateId'] = $report->id;
        }

return $dto;
    }

    private function money(string $amount, ?string $currency, int $exponent): array
    {
        return ['currency' => $currency, 'exponent' => $exponent, 'amountMinor' => bcmul($amount, bcpow('10', (string) $exponent, 0), 0)];
    }

    private function operation(string $kind, string $resource, string $url, ?string $status = 'complete'): Operation
    {
        return Operation::create(['kind' => $kind, 'status' => $status, 'resource_id' => $resource, 'user_id' => auth()->id(), 'principal_hash' => $this->access->principal(), 'result_url' => $url]);
    }

    private function operationDto(Operation $op): array
    {
        $status = $op->status;
        if ($op->kind === 'export') {
            $job = ExportJob::find($op->resource_id);
            $status = match ($job?->status) {
                'ready' => 'complete','failed' => 'failed','cancelled' => 'cancelled','rendering' => 'running',default => 'queued'
            };
        }

return ['id' => $op->id, 'kind' => $op->kind, 'status' => $status, 'pollAfterSeconds' => 1, 'resultUrl' => $op->result_url];
    }

    private function accepted(Operation $op)
    {
        return response()->json($this->operationDto($op), 202)->header('Location', '/api/v1/operations/'.$op->id);
    }

    public function flows()
    {
        $items = app(PublicFlows::class)->all(session('locale', 'en'))->map(function ($flow) {
            $m = $flow->activeRelease->manifest;

            return ['id' => $flow->id, 'key' => $flow->slug, 'releaseId' => $flow->active_release_id, 'name' => $flow->name, 'availableLocales' => ($m['source_preview'] ?? false) ? ['en'] : array_keys($m['translations'] ?? [])];
        });

        return response()->json($items);
    }

    public function create(Request $r)
    {
        if ($r->has('flow_id')) {
            return app(AssessmentController::class)->store($r);
        }$this->strict($r, ['flowId', 'locale', 'projectId']);
        $d = $r->validate(['flowId' => 'required|uuid', 'locale' => 'required|in:'.implode(',', array_keys(config('platform.locales'))), 'projectId' => 'nullable|uuid']);
        $result = app(Idempotent::class)->execute('create:'.$this->access->principal(), $this->key($r), $d, function () use ($d) {
            [$run,$token] = app(RunService::class)->start(Flow::findOrFail($d['flowId']), $d['locale'], $d['projectId'] ?? null);

            return ['id' => $run->id, 'recovery' => Crypt::encryptString($token)];
        });
        $run = AssessmentRun::findOrFail($result['id']);
        $this->access->assert($run);

        return response()->json($this->run($run), 201)->header('ETag', '"'.$run->revision.'"')->header('X-Recovery-Code', Crypt::decryptString($result['recovery']));
    }

    public function show(AssessmentRun $run)
    {
        $this->access->assert($run);

        return response()->json($this->run($run))->header('ETag', '"'.$run->revision.'"');
    }

    public function answers(Request $r, AssessmentRun $run)
    {
        $this->access->assert($run);
        $this->strict($r, ['answers']);
        $d = $r->validate(['answers' => 'required|array|min:1|max:100', 'answers.*.questionId' => 'required|string|max:100', 'answers.*.state' => 'required|in:known,unknown,not_decided,not_applicable', 'answers.*.value' => 'sometimes', 'answers.*.provenance' => 'sometimes|array', 'answers.*.notApplicableReason' => 'sometimes|string|max:500']);
        foreach ($d['answers'] as $answer) {
            abort_if(array_diff(array_keys($answer), ['questionId', 'state', 'value', 'notApplicableReason', 'provenance']), 422);
        }abort_if(strlen(json_encode($d['answers'])) > config('platform.max_answer_bytes'), 413);
        $saved = app(BatchAnswers::class)->save($run, $d['answers'], $this->revision($r), $this->key($r));

        return response()->json($this->run($saved))->header('ETag', '"'.$saved->revision.'"');
    }

    public function generate(Request $r, AssessmentRun $run)
    {
        if (! $r->has('locale')) {
            return app(AssessmentController::class)->report($r, $run);
        }$this->access->assert($run);
        $this->strict($r, ['locale', 'allowPartial']);
        $d = $r->validate(['locale' => 'required|in:'.implode(',', array_keys(config('platform.locales'))), 'allowPartial' => 'sometimes|boolean']);
        $revision = $this->revision($r);
        $key = $this->key($r);
        $result = app(Idempotent::class)->execute('wire-report:'.$run->id, $key, ['revision' => $revision] + $d, function () use ($run, $d, $revision, $key) {
            $previous = session('locale');
            session(['locale' => $d['locale']]);
            try {
                $report = app(ReportService::class)->generate($run, $revision, hash('sha256', 'wire:'.$key));
                if ($report->status === 'partial' && ! ($d['allowPartial'] ?? false)) {
                    throw ValidationException::withMessages(['allowPartial' => 'Explicitly acknowledge a partial report or resolve the missing evidence.']);
                }$op = $this->operation('report', $report->id, '/api/v1/reports/'.$report->id);

                return ['id' => $op->id];
            } finally {
                session(['locale' => $previous]);
            }
        });

        return $this->accepted(Operation::findOrFail($result['id']));
    }

    public function result(AssessmentReport $report)
    {
        $this->access->assert($report->run);

        return response()->json($this->report($report));
    }

    public function estimate(AssessmentReport $report)
    {
        $this->access->assert($report->run);
        $p = $report->output['price'] ?? [];
        $book = $report->price_book_id ? PriceBook::find($report->price_book_id) : null;
        $exponent = $report->snapshot['currency_exponent'] ?? $book?->exponent ?? 2;
        $lines = [];
        foreach ($p['lines'] ?? [] as $line) {
            $lines[] = ['itemId' => $line['item_id'], 'unit' => $line['unit'], 'quantity' => $line['quantity'], 'rate' => $line['rate'], 'amount' => $this->money($line['amount'], $p['currency'], $exponent), 'evidence' => $this->evidence($report, $line['sources'] ?? [], [$line['item_id']])];
        }$out = ['id' => $report->id, 'runId' => $report->run_id, 'inputRevision' => $report->input_revision, 'status' => $report->valid_until?->isPast() ? 'expired' : (($p['status'] ?? null) === 'complete' ? ($report->status === 'partial' ? 'partial' : 'complete') : 'manual_review'), 'priceBookId' => $report->price_book_id, 'currency' => $p['currency'] ?? null, 'lines' => $lines, 'unpricedItems' => $p['missing'] ?? $report->output['unresolved'] ?? [], 'createdAt' => $report->created_at->toIso8601String(), 'validUntil' => $report->valid_until?->toIso8601String(), 'evidence' => $this->evidence($report, array_column($report->output['answers'] ?? [], 'question_id'))];
        if (isset($p['total'])) {
            $out['total'] = $this->money($p['total'], $p['currency'], $exponent);
        }if ($report->baseline_id) {
            $out['supersedesId'] = $report->baseline_id;
        }

return response()->json($out);
    }

    public function reprice(Request $r, AssessmentReport $report)
    {
        $this->access->assert($report->run);
        $this->strict($r, ['acknowledgeRevision']);
        $r->validate(['acknowledgeRevision' => 'required|boolean']);
        abort_unless($r->input('acknowledgeRevision') === true, 422);
        abort_unless($report->input_revision === $this->revision($r), 412);
        $key = $this->key($r);
        $result = app(Idempotent::class)->execute('wire-reprice:'.$report->id, $key, ['acknowledgeRevision' => true], function () use ($report, $key) {
            $run = app(RunService::class)->scenario($report);
            $new = app(ReportService::class)->generate($run, $run->revision, hash('sha256', 'wire:'.$key), $report);
            $op = $this->operation('reprice', $new->id, '/api/v1/estimates/'.$new->id);

            return ['id' => $op->id];
        });

        return $this->accepted(Operation::findOrFail($result['id']));
    }

    public function exports(Request $r, AssessmentReport $report)
    {
        $this->access->assert($report->run);
        $this->strict($r, ['format']);
        $d = $r->validate(['format' => 'required|in:json,html,pdf']);
        abort_if($d['format'] === 'pdf', 422, 'PDF publication is blocked until multiscript font/render review; JSON and printable HTML are available.');
        $key = $this->key($r);
        $result = app(Idempotent::class)->execute('wire-export:'.$report->id, $key, $d, function () use ($report, $d) {
            $job = ExportJob::firstOrCreate(['report_id' => $report->id, 'format' => $d['format']], ['status' => 'queued']);
            if (in_array($job->status, ['queued', 'failed'], true)) {
                RenderExport::dispatch($job->id);
            }$op = $this->operation('export', $job->id, '/api/v1/exports/'.$job->id, $job->status === 'ready' ? 'complete' : 'queued');

            return ['id' => $op->id];
        });

        return $this->accepted(Operation::findOrFail($result['id']));
    }

    public function export(ExportJob $export)
    {
        $report = $export->report;
        $this->access->assert($report->run);

        return response()->json(['id' => $export->id, 'reportId' => $report->id, 'locale' => $report->output['locale'] ?? $report->run->locale, 'status' => $export->status, 'expiresAt' => $report->run->expires_at?->toIso8601String(), 'downloadUrl' => $export->status === 'ready' ? url('/api/v1/exports/'.$export->id.'/download') : null, 'checksum' => $export->status === 'ready' && Storage::disk('local')->exists($export->path) ? hash_file('sha256', Storage::disk('local')->path($export->path)) : null]);
    }

    public function poll(Operation $operation)
    {
        if ($operation->kind === 'publish') {
            abort_unless(auth()->user()?->capable('publisher') && $operation->user_id === auth()->id(), 403);
        } elseif ($operation->kind === 'export') {
            $export = ExportJob::findOrFail($operation->resource_id);
            $this->access->assert($export->report->run);
        } else {
            $report = AssessmentReport::findOrFail($operation->resource_id);
            $this->access->assert($report->run);
        }

return response()->json($this->operationDto($operation));
    }

    public function draft(Flow $flow)
    {
        abort_unless(AdminWorkspace::allowed('flows', auth()->user()), 403);

        return response()->json(['id' => $flow->id, 'revision' => $flow->revision, 'validationHash' => app(PublicationService::class)->hash($flow), 'reviewEvidenceId' => $flow->review_evidence_id, 'reviewedHash' => $flow->reviewed_hash])->header('ETag', '"'.$flow->revision.'"');
    }

    public function publish(Request $r, Flow $flow)
    {
        abort_unless(auth()->user()?->capable('publisher'), 403);
        $this->strict($r, ['validationHash', 'reviewEvidenceId', 'effectiveAt']);
        $d = $r->validate(['validationHash' => 'required|string|size:64', 'reviewEvidenceId' => 'required|uuid', 'effectiveAt' => 'nullable|date']);
        abort_unless($flow->revision === $this->revision($r), 412);
        abort_unless(hash_equals($flow->reviewed_hash ?? '', $d['validationHash']) && $flow->review_evidence_id === $d['reviewEvidenceId'], 422, 'Exact independent review evidence is required.');
        abort_if(isset($d['effectiveAt']) && Carbon::parse($d['effectiveAt'])->isFuture(), 422, 'Scheduled publication needs an approved operating policy; publish now or leave the reviewed draft.');
        $result = app(Idempotent::class)->execute('wire-publish:'.$flow->id,$this->key($r),$d,function () use ($flow) {
            $release = app(PublicationService::class)->publish($flow,auth()->user());
            $op = $this->operation('publish',$release->id,'/api/v1/flows');

            return ['id' => $op->id];
        });

        return $this->accepted(Operation::findOrFail($result['id']));
    }
}
