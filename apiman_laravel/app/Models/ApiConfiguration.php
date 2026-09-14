<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiConfiguration extends Model
{
    protected $table = 'api_configurations';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'base_url',
        'auth_type',
        'auth_username',
        'auth_password',
        'auth_token',
        'request_method',
        'request_body_format',
        'endpoint',
        'header_key',
        'header_value',
        'param_key',
        'param_value',
        'user_id',
    ];
}
