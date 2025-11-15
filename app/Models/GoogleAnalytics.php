<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class GoogleAnalytics extends Model
{
    protected $table = 'analytics';
    protected $primaryKey = 'analytics_id';

    // Pola, które mogą być masowo przypisywane (fillable)
    // To jest BARDZO WAŻNE dla metody Users::create()
    protected $fillable = [
        'analytics_tag'
    ];

    
    public $timestamps = true;
    //const CREATED_AT = 'creation_date';
    //const UPDATED_AT = 'last_update'; 
}