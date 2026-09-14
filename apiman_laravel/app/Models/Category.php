<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'category';
    public $timestamps = false;

    protected $fillable = [
        'description',
    ];

    public function syncApis(): HasMany
    {
        return $this->hasMany(SyncApi::class, 'category_id', 'id');
    }
}
