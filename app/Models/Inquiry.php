<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    public const STATUSES = ['new', 'read', 'archived'];

    public const STATUS_LABELS = [
        'new' => 'Pending',
        'read' => 'Reviewed',
        'archived' => 'Resolved',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];
}
