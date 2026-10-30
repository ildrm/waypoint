<?php

namespace App\Models;

class ExportJob extends PlatformModel
{
    protected $casts = ['attempts' => 'integer'];

    public function report()
    {
        return $this->belongsTo(AssessmentReport::class, 'report_id');
    }
}
