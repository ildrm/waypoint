<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditEvent extends Model
{
    protected $guarded = [];

    protected $casts = ['metadata' => 'array'];
}
