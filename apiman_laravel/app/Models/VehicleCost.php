<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCost extends Model
{
    protected $table = 'acc_vehicle_cost';
    public $timestamps = false;

    protected $fillable = [
        'vehicle_no',
        'cost_category',
        'amount',
        'cost_date',
        'description',
    ];
}
