<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    protected $table = 'form_contact';
    protected $primaryKey = 'form_id';

    protected $fillable = [
        'form_email',
        'form_email_alias',
        'form_return_email_status',
        'form_return_message'
    ];
    
    public $timestamps = true;
}