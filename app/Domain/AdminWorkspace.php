<?php

namespace App\Domain;

use App\Jobs\RenderExport;
use App\Models\AnalyticsEvent;
use App\Models\AssessmentReport;
use App\Models\AssessmentRun;
use App\Models\AuditEvent;
use App\Models\CatalogItem;
use App\Models\ExportJob;
use App\Models\Flow;
use App\Models\FlowRelease;
use App\Models\ParameterDefinition;
use App\Models\ParameterOverride;
use App\Models\PriceBook;
use App\Models\SupportRequest;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminWorkspace
{
    public const SECTIONS = ['flows' => 'content_author', 'translations' => 'translator', 'prices' => 'price_editor', 'catalog' => 'content_author', 'general' => 'content_author', 'overrides' => 'content_author', 'sessions' => 'support', 'support' => 'support', 'staff' => 'security_admin', 'audit' => 'reviewer', 'operations' => 'support'];

    public static function allowed(string $section, ?User $user): bool
    {
        if (! $user || ! isset(self::SECTIONS[$section])) {
            return false;
        }$alternatives = ['flows' => ['reviewer', 'publisher'], 'translations' => ['language_reviewer'], 'prices' => ['commercial_approver'], 'catalog' => ['reviewer'], 'support' => ['privacy_operator']];
        foreach (array_merge([self::SECTIONS[$section]], $alternatives[$section] ?? []) as $cap) {
            if ($user->capable($cap)) {
                return true;
            }
        }

        return false;
    }

    public static function canOperate(string $section, string $op, ?User $user): bool
    {
        $cap = match ($section) {
            'flows' => match ($op) {
                'save','clone' => 'content_author','review' => 'reviewer','publish','rollback','retire' => 'publisher','preview','simulate' => null,default => '__denied'
            },'translations' => $op === 'approve' ? 'language_reviewer' : 'translator','prices' => in_array($op, ['approve', 'retire'], true) ? 'commercial_approver' : 'price_editor','catalog' => $op === 'save' ? 'content_author' : 'reviewer','support' => $op === 'fulfil_delete' ? 'privacy_operator' : 'support',default => self::SECTIONS[$section] ?? '__denied'
        };

        return self::allowed($section, $user) && ($cap === null || $user?->capable($cap));
    }

    public function authorize(string $section): void
    {
        abort_unless(self::allowed($section, auth()->user()), 403);
    }

    public function data(Request $request): array
    {
        $default = null;
        foreach (self::SECTIONS as $s => $cap) {
            if (self::allowed($s, auth()->user())) {
                $default = $s;
                break;
            }
        }$section = $request->query('section', $default ?? 'flows');
        $this->authorize($section);
        $models = ['flows' => Flow::class, 'translations' => Translation::class, 'prices' => PriceBook::class, 'catalog' => CatalogItem::class, 'general' => ParameterDefinition::class, 'overrides' => ParameterOverride::class, 'sessions' => AssessmentRun::class, 'support' => SupportRequest::class, 'staff' => User::class, 'audit' => AuditEvent::class, 'operations' => ExportJob::class];
        $model = $models[$section];
        $q = $model::query();
        if ($section === 'translations') {
            $locale = $request->query('locale', 'en');
            abort_unless(isset(config('platform.locales')[$locale]), 422);
            $q->where('locale', $locale);
            if ($request->filled('search')) {
                $q->where(fn ($q) => $q->where('key', 'like', '%'.$request->query('search').'%')->orWhere('source_text', 'like', '%'.$request->query('search').'%'));
            }
        }if ($request->filled('search') && in_array($section, ['flows', 'prices', 'catalog', 'staff', 'general'], true)) {
            $q->where('name', 'like', '%'.$request->query('search').'%');
        }if ($section === 'sessions') {
            $q->select(['id', 'flow_id', 'release_id', 'revision', 'locale', 'status', 'created_at', 'updated_at', 'expires_at']);
        }$records = $q->latest('updated_at')->limit(100)->get();
        $selected = $request->filled('edit') ? $model::findOrFail($request->query('edit')) : null;
        if ($section === 'sessions' && $selected) {
            $selected->makeHidden(['answers', 'principal_hash', 'resume_token_hash']);
        }$editor = $this->editor($section, $selected);
        $flows = Flow::all(['id', 'name', 'slug']);
        $parameters = ParameterDefinition::all();
        $releases = $section === 'flows' && $selected ? FlowRelease::where('flow_id', $selected->id)->latest('version')->get(['id', 'version', 'status', 'hash', 'published_at']) : collect();
        $effective = $section === 'overrides' && ($request->filled('flow_id') || ($section === 'overrides' && $selected)) ? app(SettingsResolver::class)->effective(Flow::findOrFail($request->query('flow_id') ?? $selected?->flow_id)->id) : [];
        $coverage = [];
        if ($section === 'flows' && $selected) {
            $coverage = app(TranslationManager::class)->coverage($selected->id, app(TranslationManager::class)->sourceKeys($selected->bank));
        }if ($section === 'translations') {
            $coverage = app(TranslationManager::class)->coverage('ui', SourceCopy::keys());
        }$summary = ['flows' => Flow::count(), 'runs' => AssessmentRun::count(), 'reports' => AssessmentReport::count(), 'pending_exports' => ExportJob::whereIn('status', ['queued', 'failed'])->count(), 'events' => AnalyticsEvent::count()];
        $permittedOperations = array_values(array_filter(['save', 'clone', 'review', 'publish', 'preview', 'rollback', 'approve', 'reset', 'simulate', 'retire', 'suspend', 'restore', 'retry', 'fulfil_delete'], fn ($op) => self::canOperate($section, $op, auth()->user())));
        $readiness = ['production' => false, 'reason' => 'Reviewed business content, all14 locales, operational policy and deployment validation are required.'];

        return compact('section', 'records', 'selected', 'editor', 'flows', 'parameters', 'releases', 'effective', 'coverage', 'summary', 'readiness', 'permittedOperations');
    }

    public function editor(string $section, $m): string
    {
        $defaults = match ($section) {
            'flows' => ['slug' => 'new-flow', 'name' => 'New assessment', 'description' => 'Describe its purpose.', 'bank' => ['workflow_id' => 'WF-CUSTOM', 'questions' => []], 'rules' => []],'prices' => ['name' => 'New expert assumption book', 'version' => 1, 'currency' => 'USD', 'exponent' => 2, 'market' => 'unconfigured', 'synthetic' => false, 'rates' => [], 'internal_rates' => null, 'work_packages' => [], 'recurring' => [], 'contingency' => '0', 'discount' => '0', 'validity_days' => 30, 'effective_from' => now()->toIso8601String(), 'effective_until' => null],'catalog' => ['stable_id' => 'OFFER-NEW', 'name' => 'New offering', 'domain' => 'software', 'description' => 'Describe the actual supported scope.', 'eligibility' => ['always' => true], 'deliverables' => [], 'preferences' => [], 'dimensions' => [], 'synthetic' => false],'staff' => ['name' => '', 'email' => '', 'capabilities' => [], 'enabled' => true],default => []
        };
        $data = $m ? $m->only(array_keys($defaults)) : $defaults;

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    private function decode(string $raw, string $field): mixed
    {
        try {
            return json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            throw ValidationException::withMessages([$field => 'Enter valid typed JSON within the nesting limit.']);
        }
    }

    private function document(Request $r): array
    {
        try {
            $v = json_decode($r->input('document', '{}'), true, 32, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            throw ValidationException::withMessages(['document' => 'Use valid JSON within the depth limit.']);
        }if (! is_array($v)) {
            throw ValidationException::withMessages(['document' => 'Expected a JSON object.']);
        }

        return $v;
    }

    private function version($m, Request $r): void
    {
        if (! $m) {
            return;
        }$revision = $m->revision ?? null;
        if ($revision !== null) {
            abort_unless($r->has('revision') && (int) $r->input('revision') === $revision, 412, 'This record changed. Reload before saving.');
        }
    }

    public function operate(Request $r): array
    {
        $section = $r->validate(['section' => 'required|string', 'operation' => 'required|string'])['section'];
        $op = $r->input('operation');
        $this->authorize($section);
        abort_unless(self::canOperate($section, $op, auth()->user()), 403);

        return DB::transaction(function () use ($r, $section, $op) {
            if (in_array($section, ['flows', 'translations', 'prices', 'catalog', 'general', 'overrides'], true)) {
                BusinessLock::acquire();
            }
            $id = $r->input('id');
            $user = auth()->user();
            $result = [];
            if ($section === 'flows') {
                $m = $id ? Flow::whereKey($id)->lockForUpdate()->firstOrFail() : null;
                $this->version($m, $r);
                if ($op === 'save') {
                    $d = $this->document($r);
                    validator($d, ['slug' => 'required|alpha_dash|max:80', 'name' => 'required|string|max:150', 'description' => 'required|string|max:2000', 'bank' => 'required|array', 'rules' => 'present|array'])->validate();
                    app(FlowEngine::class)->validate($d['bank'], $d['rules']);
                    $d = array_intersect_key($d, array_flip(['slug', 'name', 'description', 'bank', 'rules']));
                    $d['last_edited_by'] = $user->id;
                    $d['bank']['name'] = $d['name'];
                    $d['bank']['description'] = $d['description'];
                    $d += ['question_count' => count($d['bank']['questions']), 'revision' => ($m?->revision ?? 0) + 1, 'status' => 'draft', 'reviewed_by' => null, 'reviewed_hash' => null];
                    if ($m) {
                        $m->update($d);
                    } else {
                        $m = Flow::create($d + ['created_by' => $user->id]);
                    }app(TranslationManager::class)->sync($m->id, app(TranslationManager::class)->sourceKeys($m->bank));
                } elseif ($op === 'clone') {
                    $m = app(PublicationService::class)->clone($m, $user);
                } elseif ($op === 'review') {
                    app(PublicationService::class)->review($m, $user);
                } elseif ($op === 'publish') {
                    app(PublicationService::class)->publish($m, $user);
                } elseif ($op === 'preview') {
                    app(PublicationService::class)->publish($m, $user, true);
                } elseif ($op === 'rollback') {
                    app(PublicationService::class)->rollback($m, FlowRelease::findOrFail($r->input('release_id')), $user);
                } elseif ($op === 'retire') {
                    abort_unless($user->capable('publisher'), 403);
                    $m->update(['status' => 'retired']);
                } elseif ($op === 'simulate') {
                    $answers = $this->decode($r->input('answers_json', '{}'), 'answers_json');
                    abort_unless(is_array($answers), 422);
                    $mfest = ['bank' => $m->bank, 'rules' => $m->rules];
                    $result['simulation'] = ['active' => array_column(app(FlowEngine::class)->active($m->bank, $answers, $mfest), 'id'), 'conflicts' => app(FlowEngine::class)->conflicts($answers), 'effects' => app(RuleEffects::class)->run($m->rules, $answers, app(FlowEngine::class)->context($answers))];
                } else {
                    abort(422);
                }$result['id'] = $m->id;
            } elseif ($section === 'translations') {
                $m = Translation::whereKey($id)->lockForUpdate()->firstOrFail();
                $this->version($m, $r);
                if ($op === 'approve') {
                    abort_unless($user->capable('language_reviewer'), 403);
                    app(TranslationManager::class)->approve($m, $user->id);
                } elseif ($op === 'save') {
                    $d = $r->validate(['text' => 'nullable|string|max:20000']);
                    $m->update(['text' => $d['text'], 'last_edited_by' => $user->id, 'revision' => $m->revision + 1, 'status' => 'draft', 'approved_source_hash' => null, 'reviewed_by' => null, 'reviewed_at' => null]);
                } else {
                    abort(422);
                }$result['id'] = $m->id;
            } elseif ($section === 'prices') {
                $m = $id ? PriceBook::whereKey($id)->lockForUpdate()->firstOrFail() : null;
                $this->version($m, $r);
                if ($op === 'save') {
                    $d = $this->document($r);
                    validator($d, ['name' => 'required|string|max:150', 'version' => 'required|integer|min:1', 'currency' => 'required|string|size:3', 'market' => 'required|string|max:80', 'exponent' => 'required|integer|min:0|max:6', 'synthetic' => 'required|boolean', 'rates' => 'present|array', 'work_packages' => 'present|array', 'effective_from' => 'required|date', 'effective_until' => 'nullable|date|after:effective_from', 'validity_days' => 'required|integer|min:1|max:90', 'contingency' => 'required|string', 'discount' => 'required|string'])->validate();
                    $d = array_intersect_key($d, array_flip(['name', 'version', 'currency', 'market', 'exponent', 'synthetic', 'rates', 'internal_rates', 'work_packages', 'recurring', 'contingency', 'discount', 'validity_days', 'effective_from', 'effective_until']));
                    abort_if(in_array($m?->status, ['approved', 'retired'], true), 409, 'Clone an approved book before changing its money or scope.');
                    $d['last_edited_by'] = $user->id;
                    $d['revision'] = ($m?->revision ?? 0) + 1;
                    if ($m) {
                        $m->fill($d);
                    } else {
                        $m = new PriceBook($d + ['created_by' => $user->id]);
                    }app(ProjectEstimator::class)->validateBook($m);
                    $m->save();
                } elseif ($op === 'clone') {
                    $m = $m->replicate(['approved_by']);
                    $m->status = 'draft';
                    $m->version++;
                    $m->created_by = $user->id;
                    $m->save();
                } elseif ($op === 'approve') {
                    abort_unless($user->capable('commercial_approver'), 403);
                    abort_if(($m->created_by === $user->id || $m->last_edited_by === $user->id), 422, 'A separate commercial reviewer must approve this book.');
                    abort_unless($m->status === 'draft', 409, 'Approve a new draft version; retired books remain historical.');
                    app(ProjectEstimator::class)->validateBook($m);
                    $overlap = PriceBook::where('status', 'approved')->whereKeyNot($m->id)->where('currency', $m->currency)->where('market', $m->market)->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>', $m->effective_from))->when($m->effective_until, fn ($q) => $q->where('effective_from', '<', $m->effective_until))->exists();
                    abort_if($overlap, 422, 'Retire overlapping validity before approving.');
                    $m->update(['status' => 'approved', 'approved_by' => $user->id]);
                } elseif ($op === 'retire') {
                    abort_unless($user->capable('commercial_approver'), 403);
                    $m->update(['status' => 'retired']);
                } else {
                    abort(422);
                }$result['id'] = $m->id;
            } elseif ($section === 'catalog') {
                $m = $id ? CatalogItem::whereKey($id)->lockForUpdate()->firstOrFail() : null;
                $this->version($m, $r);
                if ($op === 'save') {
                    $d = $this->document($r);
                    validator($d, ['stable_id' => 'required|string|max:100', 'name' => 'required|string|max:150', 'domain' => 'required|in:software,startup,education,service', 'description' => 'required|string|max:2000', 'eligibility' => 'required|array', 'deliverables' => 'present|array', 'preferences' => 'present|array', 'synthetic' => 'required|boolean'])->validate();
                    app(Predicate::class)->evaluate($d['eligibility'], []);
                    $d = array_intersect_key($d, array_flip(['stable_id', 'name', 'domain', 'description', 'eligibility', 'deliverables', 'preferences', 'dimensions', 'synthetic']));
                    $d['last_edited_by'] = $user->id;
                    $d['revision'] = ($m?->revision ?? 0) + 1;
                    $d['status'] = 'draft';
                    $d['version'] = ($m?->version ?? 0) + 1;
                    if ($m) {
                        $m->update($d);
                    } else {
                        $m = CatalogItem::create($d);
                    }
                } elseif ($op === 'approve') {
                    abort_unless($user->capable('reviewer'), 403);
                    abort_if($m->last_edited_by === $user->id, 422, 'A separate reviewer must approve the edited catalog item.');
                    $m->update(['status' => 'approved']);
                } elseif ($op === 'retire') {
                    $m->update(['status' => 'retired']);
                } else {
                    abort(422);
                }$result['id'] = $m->id;
            } elseif ($section === 'general') {
                $m = ParameterDefinition::whereKey($id)->lockForUpdate()->firstOrFail();
                $this->version($m, $r);
                if (in_array($m->id, ['PARAM-CONTINGENCY', 'PARAM-DISCOUNT'], true)) {
                    abort_unless($user->capable('commercial_approver'), 403);
                }try {
                    $value = $this->decode($r->input('value_json'), 'value_json');
                } catch (\Throwable) {
                    throw ValidationException::withMessages(['value_json' => 'Enter a typed JSON scalar, including quoted decimals.']);
                }app(SettingsResolver::class)->validate($m, $value);
                $m->update(['value' => $value, 'revision' => $m->revision + 1]);
            } elseif ($section === 'overrides') {
                $f = Flow::findOrFail($r->input('flow_id'));
                $d = ParameterDefinition::findOrFail($r->input('parameter_id'));
                if (in_array($d->id, ['PARAM-CONTINGENCY', 'PARAM-DISCOUNT'], true)) {
                    abort_unless($user->capable('commercial_approver'), 403);
                }abort_unless($d->allow_override, 422);
                if ($op === 'reset') {
                    app(SettingsResolver::class)->reset($f->id, $d->id);
                } elseif ($op === 'save') {
                    $value = json_decode($r->input('value_json'), true, 16, JSON_THROW_ON_ERROR);
                    app(SettingsResolver::class)->validate($d, $value);
                    ParameterOverride::updateOrCreate(['flow_id' => $f->id, 'parameter_id' => $d->id], ['value' => $value, 'reason' => $r->input('reason', 'Explicit staff override')]);
                } else {
                    abort(422);
                }
            } elseif ($section === 'staff') {
                $m = $id ? User::whereKey($id)->lockForUpdate()->firstOrFail() : null;
                $this->version($m, $r);
                $d = $this->document($r);
                validator($d, ['name' => 'required|string|max:100', 'email' => 'required|email|max:200', 'capabilities' => 'present|array', 'capabilities.*' => 'in:'.implode(',', config('platform.capabilities')), 'enabled' => 'required|boolean'])->validate();
                abort_unless($m, 422, 'Create staff using the secure CLI bootstrap command, then manage capabilities here.');
                abort_if($m->id === $user->id && (! $d['enabled'] || ! in_array('security_admin', $d['capabilities'], true)), 422, 'A security administrator cannot revoke their own final access.');
                $m->update(array_intersect_key($d, array_flip(['name', 'email', 'capabilities', 'enabled'])) + ['revision' => $m->revision + 1]);
                $result['id'] = $m->id;
            } elseif ($section === 'sessions') {
                $m = AssessmentRun::findOrFail($id);
                if ($op === 'suspend') {
                    $m->update(['status' => 'suspended']);
                } elseif ($op === 'restore') {
                    $m->update(['status' => 'active']);
                } else {
                    abort(422);
                }$result['id'] = $m->id;
            } elseif ($section === 'support') {
                $m = SupportRequest::findOrFail($id);
                if ($op === 'fulfil_delete') {
                    abort_unless($user->capable('privacy_operator'), 403);
                    abort_unless($m->type === 'privacy_delete' && $m->consent && $m->run_id, 422, 'Verified deletion consent and linked run are required.');
                    app(PrivacyService::class)->erase(AssessmentRun::findOrFail($m->run_id), $user);
                    $m->update(['status' => 'fulfilled', 'detail' => 'Erasure fulfilled; content removed.']);
                } elseif ($op === 'save') {
                    $m->update($r->validate(['status' => 'required|in:requested,in_review,waiting,fulfilled,declined', 'assigned_to' => 'nullable|exists:users,id']));
                } else {
                    abort(422);
                }$result['id'] = $m->id;
            } elseif ($section === 'operations') {
                $m = ExportJob::findOrFail($id);
                abort_unless($op === 'retry', 422);
                $m->update(['status' => 'queued', 'error_code' => null]);
                RenderExport::dispatch($m->id);
                $result['id'] = $m->id;
            } else {
                abort(422);
            }Audit::record('admin.'.$section.'.'.$op, $result['id'] ?? $id, ['status' => 'saved']);

            return $result;
        });
    }
}
