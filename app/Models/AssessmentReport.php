<?php

namespace App\Models;

class AssessmentReport extends PlatformModel
{
    protected $hidden = ['internal_trace'];

    protected $casts = ['snapshot' => 'array', 'output' => 'array', 'internal_trace' => 'array', 'valid_until' => 'datetime'];

    public function run()
    {
        return $this->belongsTo(AssessmentRun::class, 'run_id');
    }

    protected static function booted(): void
    {
        static::updating(function ($m) {
            if ($m->isDirty(['snapshot', 'output', 'input_revision', 'price_book_id'])) {
                throw new \LogicException('Issued reports are immutable.');
            }
        });
    }
}
