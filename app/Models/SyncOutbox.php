<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncOutbox extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
    ];
}
