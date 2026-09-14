<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    protected $table = 'acc_payable';
    protected $primaryKey = 'payable_id';
    public $timestamps = false;

    protected $fillable = [
        'due_date',
        'project_id',
        'payee_id',
        'payable_amount',
        'description',
        'expense_category',
        'period_from',
        'period_to',
        'added_by',
        'payable_status',
    ];
}
