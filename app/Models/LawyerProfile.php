<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LawyerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'professional_title',
        'short_bio',
        'biography',
        'photo_path',
        'phone',
        'public_email',
        'office_address',
        'education',
        'professional_experience',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
