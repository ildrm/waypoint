<?php

namespace App\Domain;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TranslationManager
{
    public function sourceKeys(array $bank): array
    {
        $keys = [];
        foreach (['name', 'description'] as $field) {
            if (isset($bank[$field])) {
                $keys['flow.'.$field] = $bank[$field];
            }
        }foreach ($bank['questions'] as $q) {
            foreach (['stage', 'purpose', 'unit_period_range'] as $field) {
                if (isset($q[$field])) {
                    $keys[$q['translation_keys']['wording'].'.'.$field] = $q[$field];
                }
            }$keys[$q['translation_keys']['wording']] = $q['wording'];
            $keys[$q['translation_keys']['help']] = $q['help'];
            foreach ($q['schema']['properties'] ?? [] as $field => $s) {
                $prefix = $q['translation_keys']['wording'].'.field.'.$field;
                $keys[$prefix.'.label'] = $s['title'] ?? ucwords(str_replace('_', ' ', $field));
                if (isset($s['description'])) {
                    $keys[$prefix.'.description'] = $s['description'];
                }foreach ($s['enum'] ?? [] as $v) {
                    if ($v !== null) {
                        $keys[$prefix.'.option.'.(string) $v] = ucwords(str_replace('_', ' ', (string) $v));
                    }
                }
            }foreach ($q['options'] ?? [] as $o) {
                $keys[$o['translation_key']] = $o['label'];
            }
        }

        return $keys;
    }

    public function sync(string $scope, array $keys): void
    {
        DB::transaction(function () use ($scope, $keys) {
            BusinessLock::acquire();
            foreach ($keys as $key => $source) {
                $hash = hash('sha256', $source);
                foreach (array_keys(config('platform.locales')) as $locale) {
                    $t = Translation::firstOrNew(['scope' => $scope, 'key' => $key, 'locale' => $locale]);
                    if (! $t->exists || $t->source_hash !== $hash) {
                        $t->source_text = $source;
                        $t->source_hash = $hash;
                        $t->status = $t->exists ? 'stale' : ($locale === 'en' ? 'draft' : 'missing');
                        $t->text = $locale === 'en' ? $source : $t->text;
                        $t->approved_source_hash = null;
                        $t->reviewed_by = null;
                        $t->reviewed_at = null;
                        $t->revision = ($t->revision ?? 0) + 1;
                        $t->critical = true;
                        $t->save();
                    }
                }
            }
        });
    }

    public function approve(Translation $translation, int $reviewer): void
    {
        abort_unless(User::find($reviewer)?->capable('language_reviewer'), 403);
        if ($translation->last_edited_by === $reviewer) {
            throw ValidationException::withMessages(['text' => 'A separate language reviewer must approve the edited translation.']);
        }if (! $translation->text) {
            throw ValidationException::withMessages(['text' => 'Missing translation cannot be approved.']);
        }preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)[},]/', $translation->source_text, $source);
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)[},]/', $translation->text, $target);
        preg_match_all('/(?<![a-zA-Z0-9]):([a-zA-Z_][a-zA-Z0-9_]*)/', $translation->source_text, $sc);
        preg_match_all('/(?<![a-zA-Z0-9]):([a-zA-Z_][a-zA-Z0-9_]*)/', $translation->text, $tc);
        $source[1] = array_merge($source[1], $sc[1]);
        $target[1] = array_merge($target[1], $tc[1]);
        sort($source[1]);
        sort($target[1]);
        if ($source[1] !== $target[1]) {
            throw ValidationException::withMessages(['text' => 'Translation placeholders must match source.']);
        }$formatter = new \MessageFormatter($translation->locale, $translation->text);
        if (! $formatter) {
            throw ValidationException::withMessages(['text' => 'Invalid ICU message format.']);
        }$translation->update(['status' => 'approved', 'approved_source_hash' => $translation->source_hash, 'reviewed_by' => $reviewer, 'reviewed_at' => now()]);
    }

    public function coverage(string $scope, array $keys): array
    {
        $coverage = [];
        foreach (array_keys(config('platform.locales')) as $locale) {
            $rows = Translation::where('scope', $scope)->where('locale', $locale)->whereIn('key', array_keys($keys))->get()->keyBy('key');
            $missing = [];
            foreach ($keys as $key => $source) {
                $row = $rows->get($key);
                if (! $row || $row->status !== 'approved' || $row->approved_source_hash !== hash('sha256', $source)) {
                    $missing[] = $key;
                }
            }$coverage[$locale] = ['required' => count($keys), 'approved' => count($keys) - count($missing), 'missing' => $missing];
        }

        return $coverage;
    }

    public function snapshot(string $scope, array $keys): array
    {
        $out = [];
        foreach (Translation::where('scope', $scope)->where('status', 'approved')->get() as $t) {
            if (isset($keys[$t->key]) && $t->approved_source_hash === hash('sha256', $keys[$t->key])) {
                $out[$t->locale][$t->key] = $t->text;
            }
        }

        return $out;
    }

    public function text(string $source, array $manifest, string $locale, array $values = []): string
    {
        $key = SourceCopy::key($source);
        $text = $manifest['ui_translations'][$locale][$key] ?? null;
        if ($text === null) {
            if (! ($manifest['source_preview'] ?? false)) {
                throw ValidationException::withMessages(['locale' => 'Report copy needs review in this locale.']);
            }$text = $source;
        }if (! $values) {
            return $text;
        }$formatter = new \MessageFormatter($locale, $text);
        $result = $formatter->format($values);

        return $result === false ? $text : $result;
    }

    public function localize(array $bank, array $translations, string $locale, bool $preview): array
    {
        foreach (['name', 'description'] as $field) {
            if (isset($translations[$locale]['flow.'.$field])) {
                $bank[$field] = $translations[$locale]['flow.'.$field];
            }
        }foreach ($bank['questions'] as &$q) {
            foreach (['stage', 'purpose', 'unit_period_range'] as $field) {
                if (isset($translations[$locale][$q['translation_keys']['wording'].'.'.$field])) {
                    $q[$field] = $translations[$locale][$q['translation_keys']['wording'].'.'.$field];
                }
            }foreach (['wording', 'help'] as $field) {
                $key = $q['translation_keys'][$field];
                if (isset($translations[$locale][$key])) {
                    $q[$field] = $translations[$locale][$key];
                } elseif (! $preview) {
                    throw ValidationException::withMessages(['locale' => 'Critical copy is not approved for this locale.']);
                }
            }if ($q['id'] === 'Q-ST-C07') {
                $q['wording'] = str_replace(': [show the exact questions and selected answers]. ', '. ', $q['wording']);
            }foreach ($q['schema']['properties'] ?? [] as $field => $s) {
                $prefix = $q['translation_keys']['wording'].'.field.'.$field;
                $q['schema']['properties'][$field]['title'] = $translations[$locale][$prefix.'.label'] ?? ($s['title'] ?? ucwords(str_replace('_', ' ', $field)));
                if (isset($s['description'])) {
                    $q['schema']['properties'][$field]['description'] = $translations[$locale][$prefix.'.description'] ?? $s['description'];
                }foreach ($s['enum'] ?? [] as $v) {
                    if ($v !== null) {
                        $q['schema']['properties'][$field]['x-option-labels'][(string) $v] = $translations[$locale][$prefix.'.option.'.(string) $v] ?? ucwords(str_replace('_', ' ', (string) $v));
                    }
                }
            }foreach ($q['options'] ?? [] as $i => $o) {
                if (isset($translations[$locale][$o['translation_key']])) {
                    $q['options'][$i]['label'] = $translations[$locale][$o['translation_key']];
                } elseif (! $preview) {
                    throw ValidationException::withMessages(['locale' => 'Critical options are not approved for this locale.']);
                }
            }
        }unset($q);

        return $bank;
    }
}
