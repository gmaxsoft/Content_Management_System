<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    protected $table = 'access'; // Tabela to 'cms_access'
    protected $primaryKey = 'access_level'; // kluczem głównym jest 'access_level'
    public $timestamps = true; // True -> Jeśli tabela 'access' ma kolumny created_at/updated_at
    protected $fillable = [
        'access_name', // Nazwa uprawnienia
        'access_level', // Poziom uprawnienia (Cyfra od 1 do 20)
        'access_description' // Opis uprawnienia
    ];
}