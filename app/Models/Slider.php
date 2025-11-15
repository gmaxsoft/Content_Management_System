<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'slider';
    protected $primaryKey = 'slider_id';

    protected $fillable = [
        'slider_title',
        'slider_description',
        'slider_url',
        'slider_identifier',
        'slider_position',
        'slider_display',
        'slider_lang'
    ];

    public $timestamps = true;

    protected $casts = [
        'slider_id' => 'integer',
    ];

    public function lang()
    {
        return $this->belongsTo(Lang::class, 'slider_lang', 'lang_id');
    }
}
