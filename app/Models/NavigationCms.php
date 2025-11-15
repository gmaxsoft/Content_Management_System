<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Model for the CMS navigation items.
 * Represents the 'navigation_cms' table in the database.
 */
class NavigationCms extends Model
{
    protected $table = 'navigation_cms'; // Tabela to 'navigation_cms'
    protected $primaryKey = 'id'; // kluczem głównym jest 'id'
    public $timestamps = false; // False -> Jeśli tabela 'navigation_cms' nie ma kolumny created_at/updated_at
    protected $fillable = [     
        'text', // Tytuł nawigacji
        'href', // URL nawigacji
        'icon', // Ikona nawigacji
        'tooltip', // Podpowiedź (tooltip) nawigacji
        'target', // Cel otwarcia linku (np. _blank)
        'order', // Kolejność wyświetlania
        'child_id', // ID dziecka (dla hierarchii)
        'position', // Pozycja elementu w menu
    ];
}