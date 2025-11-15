<?php

namespace App\Controllers;

use App\Models\RouteModel;

class RouteController extends DefaultController
{
    /**
     * Wyszukuje nazwę kontrolera na podstawie podanego URL
     */
    public function getControllerName()
    {
        // Pecee SimpleRouter automatycznie parsuje dane POST
        $urlToMatch = input('url');

        if (!$urlToMatch) {
            $this->sendJsonResponse(['success' => true, 'error' => 'URL is missing.'], 400);
        }

        // Usuń wiodący i końcowy slash, aby pasował do wartości w bazie danych (np. 'users/edit/1')
        $cleanUrl = trim($urlToMatch, '/');

        // Pomijamy parametr id z URL, aby dopasować wzorzec w bazie
        // Np. 'users/edit/1' powinno pasować do wzorca 'users/edit/{id}'

        // 1. Spróbuj dopasować pełny URL (np. 'dashboard' albo 'users')
        $route = RouteModel::where('url', $cleanUrl)
            ->where('method', 'GET') // Zakładamy, że szukamy ścieżki przeglądarkowej
            ->first();

        // 2. Jeśli nie znaleziono, spróbuj dopasować wzorzec z parametrem {id}
        if (!$route) {
            // Rozbij ścieżkę na segmenty
            $segments = explode('/', $cleanUrl);

            // Jeśli jest więcej niż 2 segmenty, może to być wzorzec 'modul/akcja/{id}'
            if (count($segments) >= 2) {
                // Generujemy wzorzec bez ostatniego segmentu, zastępując go {id}
                $patternSegments = array_slice($segments, 0, -1);
                $pattern = implode('/', $patternSegments) . '/{id}';

                $route = RouteModel::where('url', $pattern)
                    ->where('method', 'GET')
                    ->first();
            }
        }

        if ($route) {
            return json_encode([
                'controller_name' => $route->controller_name,
                'action_name' => $route->action_name,
                'status' => 'success'
            ]);
        } else {
            $this->sendJsonResponse(['success' => true, 'error' => 'Ścieżka Route nie istnieje!.'], 404);
        }
    }
}
