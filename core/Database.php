<?php

namespace Core;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    private static $initialized = false;
    /**
     * Inicjalizuje połączenie z bazą danych przy użyciu Eloquent ORM.
     * Metoda ta powinna być wywołana tylko raz, aby uniknąć wielokrotnego tworzenia połączenia.
     */
    public static function init()
    {
        if (self::$initialized) {
            return; // Już zainicjowano, nic nie rób
        }

        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => $_ENV["DB_DRIVER"],
            'host'      => $_ENV["DB_HOST"],
            'database'  => $_ENV["DB_NAME"],
            'username'  => $_ENV["DB_USER"],
            'password'  => $_ENV["DB_PASS"],
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => 'cms_',
        ]);

        // Ustawienie Capsule jako globalnego dla ułatwionego dostępu do Eloquent
        $capsule->setAsGlobal();
        // Rozruch Eloquent ORM
        $capsule->bootEloquent();

        self::$initialized = true; // Oznacz jako zainicjowane
    }
}