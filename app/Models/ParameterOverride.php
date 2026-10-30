<?php

namespace App\Models;

class ParameterOverride extends PlatformModel
{
    protected $casts = ['value' => 'json'];
}
