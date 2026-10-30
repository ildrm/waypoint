<?php

namespace App\Http\Controllers;

use App\Domain\FlowEngine;
use App\Domain\Idempotent;
use App\Domain\PublicFlows;
use App\Domain\ReportService;
use App\Domain\RunAccess;
use App\Domain\RunService;
use App\Domain\TranslationManager;
use App\Jobs\RenderExport;
use App\Models\AssessmentReport;
use App\Models\AssessmentRun;
use App\Models\ExportJob;
use App\Models\Flow;
use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

final class AssessmentController
{
    public function __construct(private RunAccess $access, private FlowEngine $engine, private RunService $runs, private ReportService $reports, private TranslationManager $translations) {}

    private function bank(AssessmentRun $run): array
    {
        $m = $run->release->manifest;

        return $this->translations->localize($m['bank'], $m['translations'], session('locale', $run->locale), (bool) ($m['source_preview'] ?? false));
    }

    private function revision(Request $r): int
    {
        if ($r->is('api/*')) {
            abort_unless($r->hasHeader('If-Match'), 428);
            $value = trim($r->header('If-Match'), '"');
            abort_unless(ctype_digit($value), 422);

            return (int) $value;
        }

        return (int) $r->validate(['expected_revision' => 'required|integer|min:1'])['expected_revision'];
    }

    private function key(Request $r): string
    {
        $key = $r->is('api/*') ? $r->header('Idempotency-Key') : $r->input('idempotency_key');
        abort_unless(is_string($key) && strlen($key) >= 8 && strlen($key) <= 128, 422, 'Supply an idempotency key of 8–128 characters.');

        return $key;
    }

    private function json(Request $r): bool
    {
        return $r->is('api/*') || $r->expectsJson();
    }

    public function home(Request $r)
    {
        $flows = app(PublicFlows::class)->all(session('locale', 'en'));

        return $this->json($r) ? response()->json(['data' => $flows->map->only(['id', 'slug', 'name', 'description', 'question_count'])]) : view('home', compact('flows'));
    }

    public function index(Request $r)
    {
        $runs = $this->access->query()->with('flow')->latest()->limit(100)->get();

        return $this->json($r) ? response()->json(['data' => $runs]) : view('runs.index', compact('runs'));
    }

    public function store(Request $r)
    {
        $d = $r->validate(['flow_id' => 'required|uuid']);
        $locale = session('locale', 'en');
        $result = app(Idempotent::class)->execute('web-start:'.$this->access->principal(), $this->key($r), ['flow_id' => $d['flow_id'], 'locale' => $locale], function () use ($d, $locale) {
            [$run,$token] = $this->runs->start(Flow::findOrFail($d['flow_id']), $locale);

            return ['id' => $run->id, 'recovery' => Crypt::encryptString($token)];
        });
        $run = AssessmentRun::findOrFail($result['id']);
        $this->access->assert($run);
        $token = Crypt::decryptString($result['recovery']);
        session()->flash('resume_token', $token);

        return $this->json($r) ? response()->json(['data' => $run, 'resume_token' => $token], 201)->header('ETag', '"'.$run->revision.'"') : redirect()->route('runs.show', $run);
    }

    public function show(Request $r, AssessmentRun $run)
    {
        $this->access->assert($run);
        if ($this->json($r)) {
            return response()->json(['data' => $run, 'active_ids' => array_column($this->engine->active($run->release->manifest['bank'], $run->answers, $run->release->manifest), 'id')])->header('ETag', '"'.$run->revision.'"');
        }$bank = $this->bank($run);
        $active = $this->engine->active($bank, $run->answers, $run->release->manifest);
        $ids = array_column($active, 'id');
        $currentId = $r->query('question');
        if (! in_array($currentId, $ids, true)) {
            $currentId = null;
            foreach ($active as $q) {
                if (! in_array($run->answers[$q['id']]['state'] ?? 'unanswered', ['known', 'unknown', 'not_decided'], true)) {
                    $currentId = $q['id'];
                    break;
                }
            }$currentId ??= $ids[0] ?? null;
        }if (! $currentId) {
            return redirect()->route('runs.review', $run);
        }$question = $active[array_search($currentId, $ids, true)];
        if ($currentId === 'Q-ST-C07') {
            $map = array_column($bank['questions'], null, 'id');
            $question['clarifications'] = [];
            foreach ($this->engine->conflicts($run->answers) as $conflict) {
                $pair = ['message' => $conflict['message'], 'answers' => []];
                foreach ($conflict['sources'] as $id) {
                    $a = $run->answers[$id] ?? ['state' => 'unanswered', 'value' => null];
                    $labels = array_column($map[$id]['options'] ?? [], 'label', 'value');
                    $label = is_scalar($a['value']) ? ($labels[$a['value']] ?? (string) $a['value']) : json_encode($a['value'], JSON_UNESCAPED_UNICODE);
                    $pair['answers'][] = ['id' => $id, 'wording' => $map[$id]['wording'], 'label' => $label ?? $a['state']];
                }$question['clarifications'][] = $pair;
            }
        }
        $answer = $run->answers[$currentId] ?? null;
        $position = array_search($currentId, $ids, true);
        $previousId = $position > 0 ? $ids[$position - 1] : null;
        $answered = 0;
        foreach ($ids as $id) {
            if (in_array($run->answers[$id]['state'] ?? null, ['known', 'unknown', 'not_decided'], true)) {
                $answered++;
            }
        }$progress = ['answered' => $answered, 'total' => count($ids)];

        return view('runs.show', compact('run', 'bank', 'question', 'answer', 'progress', 'currentId', 'previousId'));
    }

    public function answer(Request $r, AssessmentRun $run)
    {
        $d = $r->validate(['question_id' => 'required|string|max:100', 'state' => 'required|string|max:30', 'value' => 'nullable']);
        abort_if(strlen(json_encode($r->input('value'))) > config('platform.max_answer_bytes'), 413);
        $saved = $this->runs->answer($run, $d['question_id'], $d['state'], $r->input('value'), $this->revision($r), $this->key($r), ! $r->is('api/*'));
        if ($this->json($r)) {
            return response()->json(['data' => $saved])->header('ETag', '"'.$saved->revision.'"');
        }if ($r->input('intent') === 'exit') {
            return redirect()->route('runs.index')->with('status', 'Your answer is saved.');
        }$ids = array_column($this->engine->active($saved->release->manifest['bank'], $saved->answers, $saved->release->manifest), 'id');
        $p = array_search($d['question_id'], $ids, true);
        $next = $p !== false ? ($ids[$p + 1] ?? null) : null;

        return $next ? redirect()->route('runs.show', [$saved, 'question' => $next]) : redirect()->route('runs.review', $saved);
    }

    public function review(AssessmentRun $run)
    {
        $this->access->assert($run);
        $bank = $this->bank($run);
        $answers = $run->answers;
        $activeIds = array_column($this->engine->active($bank, $answers, $run->release->manifest), 'id');
        $conflicts = $this->engine->conflicts($answers);

        return view('runs.review', compact('run', 'bank', 'answers', 'activeIds', 'conflicts'));
    }

    public function report(Request $r, AssessmentRun $run)
    {
        $report = $this->reports->generate($run, $this->revision($r), $this->key($r));

        return $this->json($r) ? response()->json(['data' => $report->only(['id', 'run_id', 'status', 'output', 'valid_until'])], 201) : redirect()->route('reports.show', $report);
    }

    public function result(Request $r, AssessmentReport $report)
    {
        $this->access->assert($report->run);
        $baseline = $report->baseline_id ? AssessmentReport::find($report->baseline_id) : null;
        if ($baseline) {
            $this->access->assert($baseline->run);
        }

        return $this->json($r) ? response()->json(['data' => $report->only(['id', 'run_id', 'status', 'output', 'valid_until'])]) : view('reports.show', compact('report', 'baseline'));
    }

    public function scenario(Request $r, AssessmentReport $report)
    {
        $this->access->assert($report->run);
        $result = app(Idempotent::class)->execute('web-scenario:'.$report->id, $this->key($r), ['baseline' => $report->id], function () use ($report) {
            $run = $this->runs->scenario($report);

            return ['id' => $run->id, 'recovery' => Crypt::encryptString(session('resume_token'))];
        });
        $run = AssessmentRun::findOrFail($result['id']);
        $this->access->assert($run);
        session()->flash('resume_token', Crypt::decryptString($result['recovery']));

        return $this->json($r) ? response()->json(['data' => $run], 201) : redirect()->route('runs.show', $run);
    }

    public function reprice(Request $r, AssessmentReport $report)
    {
        $this->access->assert($report->run);
        $key = $this->key($r);
        $result = app(Idempotent::class)->execute('web-reprice:'.$report->id, $key, ['baseline' => $report->id], function () use ($report, $key) {
            $run = $this->runs->scenario($report);
            $new = $this->reports->generate($run, $run->revision, hash('sha256', 'web:'.$key), $report);

            return ['id' => $new->id, 'recovery' => Crypt::encryptString(session('resume_token'))];
        });
        $new = AssessmentReport::findOrFail($result['id']);
        $this->access->assert($new->run);
        session()->flash('resume_token', Crypt::decryptString($result['recovery']));

        return $this->json($r) ? response()->json(['data' => $new->only(['id', 'output'])], 201) : redirect()->route('reports.show', $new);
    }

    public function resume(Request $r)
    {
        $d = $r->validate(['token' => 'required|string|size:64']);
        $run = $this->runs->resume($d['token']);

        return redirect()->route('runs.show', $run);
    }

    public function locale(Request $r)
    {
        $d = $r->validate(['locale' => 'required|string|in:'.implode(',', array_keys(config('platform.locales')))]);
        session(['locale' => $d['locale']]);

        return back();
    }

    public function export(Request $r, AssessmentReport $report)
    {
        $this->access->assert($report->run);
        $format = $r->query('format', 'json');
        abort_unless(in_array($format, ['json', 'html'], true), 422, 'Available formats: JSON and printable HTML.');
        if ($format === 'json') {
            return response()->streamDownload(fn () => print (json_encode(['id' => $report->id, 'output' => $report->output, 'valid_until' => $report->valid_until], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)), 'assessment-'.$report->id.'.json', ['Content-Type' => 'application/json']);
        }

        return response()->view('reports.export', compact('report'))->header('Content-Disposition', 'attachment; filename="assessment-'.$report->id.'.html"');
    }

    public function queueExport(Request $r, AssessmentReport $report)
    {
        $this->access->assert($report->run);
        $d = $r->validate(['format' => 'required|in:json,html']);
        $job = ExportJob::firstOrCreate(['report_id' => $report->id, 'format' => $d['format']], ['status' => 'queued']);
        if (in_array($job->status, ['queued', 'failed'], true)) {
            $job->update(['status' => 'queued', 'error_code' => null]);
            RenderExport::dispatch($job->id);
        }

        return response()->json(['operation_id' => $job->id, 'status' => $job->fresh()->status], 202);
    }

    public function download(Request $r, ExportJob $export)
    {
        $this->access->assert($export->report->run);
        abort_unless($export->status === 'ready' && $export->path, 409);

        return Storage::disk('local')->download($export->path, 'assessment.'.$export->format);
    }

    public function operation(ExportJob $export)
    {
        $this->access->assert($export->report->run);

        return response()->json(['id' => $export->id, 'status' => $export->status, 'error_code' => $export->error_code]);
    }

    public function support(Request $r)
    {
        $d = $r->validate(['type' => 'required|in:help,estimate_review,export_problem,privacy_export,privacy_delete', 'report_id' => 'nullable|uuid', 'detail' => 'nullable|string|max:4000', 'consent' => 'nullable|boolean']);
        $report = isset($d['report_id']) ? AssessmentReport::findOrFail($d['report_id']) : null;
        if ($report) {
            $this->access->assert($report->run);
        }SupportRequest::create(['run_id' => $report?->run_id, 'principal_hash' => $this->access->principal(), 'type' => $d['type'], 'detail' => $d['detail'] ?? null, 'consent' => (bool) ($d['consent'] ?? false)]);

        return back()->with('status', 'Your request has been recorded for review.');
    }
}
