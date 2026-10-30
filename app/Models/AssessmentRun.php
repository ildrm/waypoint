<?php

namespace App\Models;

class AssessmentRun extends PlatformModel
{
    protected $hidden = ['principal_hash', 'resume_token_hash'];

    protected $attributes = ['revision' => 1];

    protected $casts = ['answers' => 'array', 'revision' => 'integer', 'last_activity_at' => 'datetime', 'expires_at' => 'datetime'];

    public function release()
    {
        return $this->belongsTo(FlowRelease::class, 'release_id');
    }

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function reports()
    {
        return $this->hasMany(AssessmentReport::class, 'run_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
