<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubContractor extends Model
{
    protected $table = 'acc_sub_contractor';
    public $timestamps = false;

    protected $fillable = [
        'Sub_Contractor_Code',
        'Sub_Contractor_Name',
        'Sub_Contractor_Address',
        'Land_Phone_No',
        'Mobile',
        'Fax',
        'add_by',
        'NBT',
        'Is_VAT_Registered',
        'Registation_No',
        'VAT_For_Transport',
        'Attn_Person',
    ];
}
