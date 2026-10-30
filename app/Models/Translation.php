<?php

namespace App\Models;

class Translation extends PlatformModel
{
    protected $attributes = ['revision' => 1];

    protected $casts = ['critical' => 'boolean', 'reviewed_at' => 'datetime'];

    protected static function booted(): void
    {
        static::updating(function ($m) {
            if ($m->isDirty('text') && ! $m->isDirty('source_hash')) {
                $m->status = 'draft';
                $m->approved_source_hash = null;
                $m->reviewed_by = null;
                $m->reviewed_at = null;
            }
        });
    }
}
