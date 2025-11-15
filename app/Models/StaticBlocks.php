<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class StaticBlocks extends Model
{
    protected $table = 'static_blocks';
    protected $primaryKey = 'block_id';

    protected $fillable = [
        'block_identifier',
        'block_group',
        'block_title',
        'block_description',
        'block_lang',
        'block_display',
        'block_position'
    ];

    public $timestamps = true;

    public function lang()
    {
        return $this->belongsTo(Lang::class, 'block_lang', 'lang_id');
    }
}
