<?php

namespace App\Domain;

use App\Models\CatalogItem;
use App\Models\Flow;
use App\Models\FlowRelease;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class PublicationService
{
    public function __construct(private FlowEngine $engine, private SettingsResolver $settings, private TranslationManager $translations) {}

    public function hash(Flow $flow): string
    {
        return hash('sha256', json_encode([$flow->bank, $flow->rules, $this->settings->effective($flow->id), $this->translations->snapshot($flow->id, $this->translations->sourceKeys($flow->bank)), $this->translations->snapshot('ui', SourceCopy::keys()), CatalogItem::where('status', 'approved')->orderBy('stable_id')->get()->toArray()], JSON_THROW_ON_ERROR));
    }

    public function review(Flow $flow, User $user): void
    {
        abort_unless($user->capable('reviewer'), 403);
        $this->engine->validate($flow->bank, $flow->rules);
        if (($flow->created_by === $user->id || $flow->last_edited_by === $user->id)) {
            throw ValidationException::withMessages(['review' => 'The author cannot approve their own flow.']);
        }$flow->update(['review_evidence_id' => (string) Str::uuid(), 'reviewed_by' => $user->id, 'reviewed_hash' => $this->hash($flow), 'status' => 'reviewed']);
        Audit::record('flow.reviewed', $flow->id, ['hash' => $flow->reviewed_hash]);
    }

    public function publish(Flow $flow, ?User $user, bool $preview = false): FlowRelease
    {
        if ($preview && ! app()->environment(['local', 'testing'])) {
            abort(403, 'Source preview is only available locally.');
        }if (! $preview) {
            abort_unless($user?->capable('publisher'), 403);
        }

        return DB::transaction(function () use ($flow, $user, $preview) {
            BusinessLock::acquire();
            $f = Flow::whereKey($flow->id)->lockForUpdate()->firstOrFail();
            $this->engine->validate($f->bank, $f->rules);
            $keys = $this->translations->sourceKeys($f->bank);
            $this->translations->sync($f->id, $keys);
            $this->translations->sync('ui', SourceCopy::keys());
            if (! $preview) {
                if (! config('platform.retention_approved')) {
                    throw ValidationException::withMessages(['release' => 'Approve retention, deletion and operating policy before public publication.']);
                }if (! $f->reviewed_by || $f->reviewed_hash !== $this->hash($f)) {
                    throw ValidationException::withMessages(['release' => 'Review the exact current draft/settings before publication.']);
                }foreach ([$this->translations->coverage($f->id, $keys), $this->translations->coverage('ui', SourceCopy::keys())] as $coverage) {
                    foreach ($coverage as $locale => $row) {
                        if ($row['missing']) {
                            throw ValidationException::withMessages(['release' => 'Critical translation coverage is incomplete for '.$locale.'.']);
                        }
                    }
                }
            }
            $version = (int) FlowRelease::where('flow_id', $f->id)->max('version') + 1;
            $catalog = CatalogItem::where('status', 'approved')->when(! app()->environment(['local', 'testing']), fn ($q) => $q->where('synthetic', false))->get(['stable_id', 'name', 'domain', 'description', 'eligibility', 'deliverables', 'preferences', 'dimensions', 'version'])->toArray();
            $manifest = ['bank' => $f->bank, 'rules' => $f->rules, 'settings' => $this->settings->effective($f->id), 'translations' => $this->translations->snapshot($f->id, $keys), 'ui_translations' => $this->translations->snapshot('ui', SourceCopy::keys()), 'catalog' => $catalog, 'approved_education_catalog' => false, 'source_preview' => $preview, 'schema_version' => '1.0', 'method_version' => '1.0'];
            $r = FlowRelease::create(['flow_id' => $f->id, 'version' => $version, 'manifest' => $manifest, 'hash' => hash('sha256', json_encode($manifest, JSON_THROW_ON_ERROR)), 'status' => $preview ? 'preview' : 'published', 'published_by' => $user?->id, 'published_at' => now()]);
            $f->update(['active_release_id' => $r->id, 'status' => $preview ? 'preview' : 'published']);
            Audit::record('flow.published', $f->id, ['version' => $version, 'hash' => $r->hash, 'status' => $r->status]);

            return $r;
        });
    }

    public function rollback(Flow $flow, FlowRelease $release, User $user): void
    {
        abort_unless($user->capable('publisher'), 403);
        abort_unless($release->flow_id === $flow->id && $release->status === 'published', 422);
        $flow->update(['active_release_id' => $release->id, 'status' => 'published']);
        Audit::record('flow.rollback', $flow->id, ['version' => $release->version]);
    }

    public function clone(Flow $flow, User $user): Flow
    {
        abort_unless($user->capable('content_author'), 403);

        return Flow::create(['slug' => $flow->slug.'-'.strtolower(Str::random(6)), 'name' => $flow->name.' copy', 'description' => $flow->description, 'bank' => $flow->bank, 'rules' => $flow->rules, 'question_count' => count($flow->bank['questions']), 'created_by' => $user->id]);
    }
}
