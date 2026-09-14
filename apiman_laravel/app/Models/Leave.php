<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'acc_leave';
    public $timestamps = false;

    protected $fillable = [
        'Employee_No',
        'leave_type',
        'leave_date',
        'leave_apply_type',
        'Remarks',
        'mark_type',
        'added_by',
        'added_date',
    ];
}
