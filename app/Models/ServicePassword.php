<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class ServicePassword extends Model
{
    protected $table = 'service_passwd';
    protected $primaryKey = 'passwd_id'; 

    protected $fillable = [
        'passwd_hash'
    ];

    // Kolumny, które powinny być ukryte (np. hasła)
    protected $hidden = [
        'passwd_hash',
    ];

    public $timestamps = true;
}