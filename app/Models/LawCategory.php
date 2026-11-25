<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LawCategory extends Model
{
    protected $fillable = ['category_name', 'slug', 'description'];

    public function laws(): HasMany
    {
        return $this->hasMany(Law::class);
    }
}
