<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id'; 

    // Pola, które mogą być masowo przypisywane (fillable)
    // To jest BARDZO WAŻNE dla metody Users::create()
    protected $fillable = [
        'user_first_name',
        'user_last_name',
        'user_stand_name',
        'user_phone',
        'user_symbol',
        'user_email',
        'user_password', 
        'user_level',
        'user_active',
    ];

    // Kolumny, które powinny być ukryte (np. hasła)
    protected $hidden = [
        'user_password',
    ];

    public $timestamps = true;
    //const CREATED_AT = 'creation_date';
    //const UPDATED_AT = 'last_update'; 
}