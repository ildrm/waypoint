<?php

namespace App\Models;

class PriceBook extends PlatformModel
{
    protected $attributes = ['revision' => 1];

    protected $casts = ['rates' => 'array', 'internal_rates' => 'array', 'work_packages' => 'array', 'recurring' => 'array', 'synthetic' => 'boolean', 'effective_from' => 'datetime', 'effective_until' => 'datetime'];

    protected static function booted(): void
    {
        static::updating(function ($m) {
            if (in_array($m->getOriginal('status'), ['approved', 'retired'], true) && $m->isDirty(['name', 'version', 'rates', 'internal_rates', 'work_packages', 'currency', 'exponent', 'market', 'contingency', 'discount', 'recurring', 'synthetic', 'validity_days', 'effective_from', 'effective_until'])) {
                throw new \LogicException('Approved books must be cloned before editing.');
            }
        });
    }
}
