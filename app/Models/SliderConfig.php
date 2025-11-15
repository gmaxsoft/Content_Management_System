<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class SliderConfig extends Model
{
    protected $table = 'sliderconfig';
    protected $primaryKey = 'id';

    protected $fillable = [
        'slider_width',
        'slider_height',
        'slider_quality',
        'slider_format',
        'slider_main_id'
    ];

    public $timestamps = true;

    protected $casts = [
        'id' => 'integer',
    ];
}
