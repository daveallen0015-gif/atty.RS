<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public const ICONS = [
        'scale' => 'Scales of justice',
        'document' => 'Legal document',
        'family' => 'Family and civil',
        'property' => 'Property and land',
        'business' => 'Business and contracts',
        'labor' => 'Labor and employment',
        'shield' => 'Criminal matters',
        'estate' => 'Estate and inheritance',
    ];

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'icon',
        'image_path',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }
}
