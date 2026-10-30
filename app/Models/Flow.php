<?php

namespace App\Models;

class Flow extends PlatformModel
{
    protected $attributes = ['revision' => 1];

    protected $casts = ['bank' => 'array', 'rules' => 'array', 'revision' => 'integer'];

    public function activeRelease()
    {
        return $this->belongsTo(FlowRelease::class, 'active_release_id');
    }
}
