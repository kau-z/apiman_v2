<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'acc_expense_category';
    protected $primaryKey = 'exp_id';
    public $timestamps = false;

    protected $fillable = [
        'expense_category',
        'parent_id',
    ];
}
