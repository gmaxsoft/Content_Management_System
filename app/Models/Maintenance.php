<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $table = 'maintenance';
    protected $primaryKey = 'maintenance_id';
    
    protected $fillable = [
        'maintenance_active',
        'maintenance_ip',
        'maintenance_mode',
        'maintenance_txt'
    ];

    public $timestamps = true;
}
