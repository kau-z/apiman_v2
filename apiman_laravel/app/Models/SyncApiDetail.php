<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncApiDetail extends Model
{
    protected $table = 'sync_api_detail';
    public $timestamps = false;

    protected $fillable = [
        'main_id',
        'last_time',
        'total_records',
        'from_primary_key',
        'to_primary_key',
        'status',
        'type',
        'remarks',
        'sync_time',
    ];

    protected $casts = [
        'last_time' => 'datetime',
        'sync_time' => 'datetime',
    ];

    public function syncApi(): BelongsTo
    {
        return $this->belongsTo(SyncApi::class, 'main_id', 'main_id');
    }
}
