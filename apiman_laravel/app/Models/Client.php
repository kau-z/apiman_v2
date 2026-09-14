<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'ma_client';
    protected $primaryKey = 'Client_Code';
    public $timestamps = false;

    protected $fillable = [
        'Client_Id',
        'Client_Name',
        'Official_Contact_Dtl',
        'Project_Contact_Dtl',
        'Contact_Person_Name',
        'Contact_Person_Email',
        'Contact_Person_Phone',
        'Contact_Person_Mobile',
        'Added_By',
        'Client_Address',
        'Vat_No',
    ];
}
