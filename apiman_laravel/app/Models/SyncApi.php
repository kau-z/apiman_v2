<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyncApi extends Model
{
    protected $table = 'sync_api';
    protected $primaryKey = 'main_id';
    public $timestamps = false;

    protected $fillable = [
        'api_name',
        'category_id',
        'from_db',
        'to_db',
        'from_added_query',
        'Prefix',
        'Suffix',
        'to_added_query',
        'from_Updated_query',
        'to_Updated_query',
        'from_updated_query',
        'to_updated_query',
        'from_delete_query',
        'to_delete_query',
        'last_added_time_query',
        'last_updated_time_query',
        'last_deleted_time_query',
        'total_count_query',
        'status_to_aync_api_detail',
        'wait_till_confirmation',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->from_db = $model->from_db ?? '';
            $model->to_db = $model->to_db ?? '';
            $model->Prefix = $model->Prefix ?? '';
            $model->Suffix = $model->Suffix ?? '';
            $model->from_added_query = $model->from_added_query ?? '';
            $model->to_added_query = $model->to_added_query ?? '';

            if (isset($model->attributes['from_Updated_query']) && !isset($model->attributes['from_updated_query'])) {
                $model->attributes['from_updated_query'] = $model->attributes['from_Updated_query'];
                unset($model->attributes['from_Updated_query']);
            }
            if (isset($model->attributes['to_Updated_query']) && !isset($model->attributes['to_updated_query'])) {
                $model->attributes['to_updated_query'] = $model->attributes['to_Updated_query'];
                unset($model->attributes['to_Updated_query']);
            }

            $model->from_updated_query = $model->from_updated_query ?? '';
            $model->to_updated_query = $model->to_updated_query ?? '';
            $model->from_delete_query = $model->from_delete_query ?? '';
            $model->to_delete_query = $model->to_delete_query ?? '';
            $model->last_added_time_query = $model->last_added_time_query ?? '';
            $model->last_updated_time_query = $model->last_updated_time_query ?? '';
            $model->last_deleted_time_query = $model->last_deleted_time_query ?? '';
            $model->total_count_query = $model->total_count_query ?? '';
            $model->status_to_aync_api_detail = $model->status_to_aync_api_detail ?? 0;
            $model->wait_till_confirmation = $model->wait_till_confirmation ?? 1;
        });
    }

    public function getFromUpdatedQueryAttribute()
    {
        return $this->attributes['from_updated_query'] ?? $this->attributes['from_Updated_query'] ?? '';
    }

    public function getToUpdatedQueryAttribute()
    {
        return $this->attributes['to_updated_query'] ?? $this->attributes['to_Updated_query'] ?? '';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(SyncApiDetail::class, 'main_id', 'main_id');
    }
}
