<?php

namespace App\Models;

class AnalyticsEvent extends PlatformModel
{
    protected $casts = ['dimensions' => 'array'];
}
