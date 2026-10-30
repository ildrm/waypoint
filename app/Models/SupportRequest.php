<?php

namespace App\Models;

class SupportRequest extends PlatformModel
{
    protected $casts = ['consent' => 'boolean'];
}
