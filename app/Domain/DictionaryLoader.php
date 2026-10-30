<?php

namespace App\Domain;

use App\Models\AssessmentReport;
use App\Models\AssessmentRun;
use App\Models\Translation;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Facades\Schema;

final class DictionaryLoader implements Loader
{
    public function __construct(private Loader $loader) {}

    public function load($locale, $group, $namespace = null)
    {
        $lines = $this->loader->load($locale, $group, $namespace);
        if (app()->runningInConsole() && config('database.default') === 'sqlite' && config('database.connections.sqlite.database') !== ':memory:' && ! is_file(config('database.connections.sqlite.database'))) {
            return $lines;
        }
        if (! in_array($group, ['*', 'validation'], true) || ! Schema::hasTable('translations')) {
            return $lines;
        }if ($group === 'validation') {
            $base = $this->loader->load('en', 'validation', $namespace);
            $dict = [];
            foreach (Translation::where('scope', 'ui')->where('locale', $locale)->where('status', 'approved')->get() as $row) {
                if ($row->approved_source_hash === $row->source_hash) {
                    $dict[$row->source_text] = $row->text;
                }
            }$convert = function ($v) use (&$convert, $dict) {
                $out = [];
                foreach ($v as $key => $text) {
                    $out[$key] = is_array($text) ? $convert($text) : ($dict[$text] ?? $text);
                }

                return $out;
            };

            return array_replace_recursive($base, $lines, $convert($base));
        }
        $run = request()->route('run');
        $report = request()->route('report');
        $manifest = $run instanceof AssessmentRun ? $run->release?->manifest : ($report instanceof AssessmentReport ? $report->run->release?->manifest : null);
        if (app()->bound('business.translation_manifest')) {
            $manifest = app('business.translation_manifest');
        }$snapshot = $manifest['ui_translations'][$locale] ?? null;
        foreach (Translation::where('scope', 'ui')->where('locale', $locale)->where('status', 'approved')->get() as $row) {
            if ($row->approved_source_hash === $row->source_hash) {
                $lines[$row->source_text] = $row->text;
            }
        }if ($snapshot !== null) {
            foreach (Translation::where('scope', 'ui')->where('locale', $locale)->whereIn('key', array_keys($snapshot))->get(['key', 'source_text']) as $row) {
                $lines[$row->source_text] = $snapshot[$row->key];
            }
        }

        return $lines;
    }

    public function addNamespace($namespace, $hint)
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path)
    {
        $this->loader->addJsonPath($path);
    }

    public function namespaces()
    {
        return $this->loader->namespaces();
    }
}
