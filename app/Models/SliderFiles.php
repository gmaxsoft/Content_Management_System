<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class SliderFiles extends Model
{
    protected $table = 'slider_files';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'file_name',
        'file_type',
        'file_size',
        'file_main_id',
        'file_position'
    ];

    public $timestamps = true;

    protected $casts = [
        'file_id' => 'integer',
    ];
}