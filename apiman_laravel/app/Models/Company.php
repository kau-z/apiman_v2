<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company_profile';
    public $timestamps = false;

    protected $fillable = [
        'company_legal_name',
        'company_reg_no',
        'incorporation_date',
        'financial_year',
        'tin_no',
        'vat_svat_no',
        'nbt_reg_no',
        'epf_etf_reg_no',
        'payee_tax_no',
        'address',
        'image',
    ];
}
