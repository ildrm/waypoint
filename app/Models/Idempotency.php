<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idempotency extends Model
{
    protected $table = 'idempotencies';

    protected $guarded = [];

    protected $casts = ['response' => 'array', 'expires_at' => 'datetime'];
}
