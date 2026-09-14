<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $table = 'acc_business';
    protected $primaryKey = 'project_id';
    public $timestamps = false;

    protected $fillable = [
        'business_name',
        'status',
        'PMS_project_id',
    ];
}
