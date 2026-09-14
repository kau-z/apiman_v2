<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'acc_attendence';
    public $timestamps = false;

    protected $fillable = [
        'project_code',
        'Employee_Code',
        'Date',
        'Time',
        'Machine',
        'Location',
        'Row',
        'UpdatedDate',
    ];
}
