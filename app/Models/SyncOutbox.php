<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncOutbox extends Model
{
    protected $table = 'sync_outbox';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
    ];
}
