<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentApplication extends Model
{
    protected $table = 'payment_application';
    protected $primaryKey = 'bill_id';
    public $timestamps = false;

    protected $fillable = [
        'bill_date',
        'Client_Code',
        'Client_ID_PMS',
        'particulars',
        'amount',
        'retention_amount',
        'invoice_status',
        'added_by',
        'business_id',
    ];
}
