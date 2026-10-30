<?php

namespace App\Models;

class FlowRelease extends PlatformModel
{
    protected $casts = ['manifest' => 'array', 'published_at' => 'datetime'];

    protected static function booted(): void
    {
        static::updating(function ($m) {
            if ($m->isDirty(['manifest', 'hash', 'version'])) {
                throw new \LogicException('Released manifests are immutable.');
            }
        });
    }
}
