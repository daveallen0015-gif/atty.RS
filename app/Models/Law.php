<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Law extends Model
{
    protected $fillable = [
        'law_category_id', 'slug', 'law_type', 'law_number', 'official_title',
        'short_description', 'full_text', 'date_approved', 'official_source_url',
        'legal_status', 'document_path', 'is_published',
    ];

    protected function casts(): array
    {
        return ['date_approved' => 'date', 'is_published' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LawCategory::class, 'law_category_id');
    }

    public function relatedLaws(): HasMany
    {
        return $this->category->laws()->whereKeyNot($this->getKey())->where('is_published', true);
    }
}
