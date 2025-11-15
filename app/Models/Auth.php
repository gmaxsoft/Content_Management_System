<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    protected $table = 'users';
    protected $fillable = ['user_email', 'user_password', 'user_active', 'user_first_name', 'user_last_name'];
}