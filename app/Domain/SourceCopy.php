<?php

namespace App\Domain;

use App\Models\CatalogItem;
use App\Models\Flow;
use Illuminate\Support\Facades\Schema;

final class SourceCopy
{
    public static function key(string $source): string
    {
        return 'copy.'.hash('sha256', $source);
    }

    public static function keys(): array
    {
        $sources = array_merge(array_values(config('platform.ui_copy')), ['Unanswered', 'Known', 'Not Decided', 'Not Applicable', 'Inactive', 'Pending', 'Approved', 'Stale', 'Missing', 'Draft', 'Reviewed', 'Published', 'Retired', 'Preview', 'Partial', 'Preliminary', 'Manual Review', 'Ready', 'Failed', 'Rendering', 'Cancelled', 'Queued', 'Active', 'Suspended', 'Deleted', 'Expired', 'Yes', 'No', 'Unknown', 'To Confirm', 'Complete', 'Incomplete', 'Planning']);
        $paths = glob(resource_path('views').'/*.blade.php');
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if (str_ends_with($file->getPathname(), '.blade.php')) {
                preg_match_all('/__\(\s*([\'"])((?:(?!\1).|\\.)*?)\1/s', file_get_contents($file->getPathname()), $matches);
                foreach ($matches[2] as $s) {
                    if (! str_contains($s, '$')) {
                        $sources[] = stripcslashes($s);
                    }
                }
            }
        }
        foreach (array_merge(glob(app_path('Domain').'/*.php'), glob(app_path('Http/Controllers').'/*.php'), glob(app_path('Http/Middleware').'/*.php')) as $file) {
            foreach (token_get_all(file_get_contents($file)) as $token) {
                if (! is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                    continue;
                }$raw = substr($token[1], 1, -1);
                $s = str_replace(["\\'", '\\\\'], ["'", '\\'], $raw);
                if (substr_count($s, ' ') >= 2 && preg_match('/^[A-Z][a-zA-Z ]/', $s) && ! preg_match('/^(SELECT|VACUUM|INSERT|UPDATE|DELETE) /', $s) && strlen($s) <= 2000) {
                    $sources[] = $s;
                }
            }
        }
        $sources = array_merge($sources, ['Reported evidence for {question_id}: {answer_value}.', 'This context informs the action review: {question_id}.', 'Surface: {surface}.', 'Domain: {domain}.', 'Unknown or not decided: {question_id}.', 'Unknown field: {question_id}.{field}.', 'Applicability pending: {question_id}.']);
        if (Schema::hasTable('flows')) {
            foreach (Flow::all() as $flow) {
                $sources[] = $flow->name;
                $sources[] = $flow->description;
                $collect = function ($v) use (&$collect, &$sources) {
                    if (! is_array($v)) {
                        return;
                    }foreach ($v as $key => $text) {
                        if (in_array($key, ['message', 'text', 'name'], true) && is_string($text)) {
                            $sources[] = $text;
                        } elseif (is_array($text)) {
                            $collect($text);
                        }
                    }
                };
                foreach ($flow->rules as $r) {
                    $collect($r['effect']);
                }
            }foreach (CatalogItem::where('status', 'approved')->get() as $item) {
                $sources[] = $item->name;
                $sources[] = $item->description;
                foreach ($item->deliverables as $deliverable) {
                    if (is_string($deliverable)) {
                        $sources[] = $deliverable;
                    }
                }
            }
        }$validation = require base_path('vendor/laravel/framework/src/Illuminate/Translation/lang/en/validation.php');
        $flatten = function ($v) use (&$flatten, &$sources) {
            foreach ($v as $text) {
                if (is_array($text)) {
                    $flatten($text);
                } elseif (is_string($text)) {
                    $sources[] = $text;
                }
            }
        };
        $flatten($validation);
        $keys = [];
        foreach (array_unique($sources) as $s) {
            $keys[self::key($s)] = $s;
        }

return $keys;
    }
}
