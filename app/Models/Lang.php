<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Lang extends Model
{
    protected $table = 'lang';
    protected $primaryKey = 'lang_id';

    protected $fillable = [
        'lang_name',
        'lang_description'
    ];

    public $timestamps = true;

    protected $casts = [
        'lang_id' => 'integer',
    ];
}
