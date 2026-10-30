<?php

namespace App\Models;

class CatalogItem extends PlatformModel
{
    protected $attributes = ['revision' => 1];

    protected $casts = ['eligibility' => 'array', 'deliverables' => 'array', 'preferences' => 'array', 'dimensions' => 'array', 'synthetic' => 'boolean'];
}
