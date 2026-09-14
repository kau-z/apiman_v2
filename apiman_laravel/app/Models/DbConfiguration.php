<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DbConfiguration extends Model
{
    protected $table = 'db_configurations';
    public $timestamps = false;

    protected $fillable = [
        'active_group',
        'active_record',
        'hostname',
        'username',
        'password',
        'database',
        'dbdriver',
        'port',
        'dbprefix',
        'pconnect',
        'db_debug',
        'cache_on',
        'cachedir',
        'char_set',
        'dbcollat',
        'swap_pre',
        'autoinit',
        'stricton',
        'user_id',
    ];
}
