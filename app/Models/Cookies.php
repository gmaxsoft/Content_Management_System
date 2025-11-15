<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Cookies extends Model
{
    protected $table = 'cookies';
    protected $primaryKey = 'cookies_id';

    protected $fillable = [
        'cookies_txt',
        'cookies_active',
        'cookies_mode',
    ];

    public $timestamps = true;

    protected $casts = [
        'cookies_id' => 'integer',
    ];
}
