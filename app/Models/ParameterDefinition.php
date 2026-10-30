<?php

namespace App\Models;

class ParameterDefinition extends PlatformModel
{
    protected $attributes = ['revision' => 1];

    protected $casts = ['value' => 'json', 'constraints' => 'array', 'allow_override' => 'boolean', 'nullable' => 'boolean'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'parameter_definitions';
}
