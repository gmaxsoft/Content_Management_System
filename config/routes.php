<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Middlewares\AuthMiddleware;      // Dla routes przeglądarkowych!
use App\Middlewares\ApiAuthMiddleware;   // Dla routes API/AJAX!

use App\Models\RouteModel;

SimpleRouter::csrfVerifier(new \App\Middlewares\CsrfVerifier());
SimpleRouter::setDefaultNamespace('App\\Controllers');
SimpleRouter::group(['exceptionHandler' => \App\Handlers\CustomExceptionHandler::class], function () {

    // Trasy publiczne i stałe
    SimpleRouter::get('/', 'HomeController@index')->name('index');
    SimpleRouter::get('logout', 'AuthController@logout')->name('logout');
    SimpleRouter::get('login', 'HomeController@index')->name('login_index');
    SimpleRouter::post('login', 'AuthController@login')->name('login');

    //TRASA API do dynamicznego pobierania nazwy kontrolera
    SimpleRouter::post('/api/get-controller-by-url', 'RouteController@getControllerName')->name('api_get_controller_name');

    // Get Ip
    SimpleRouter::get('/get-my-ip', function () {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if ($ip === '::1' || $ip === '127.0.0.1' || $ip === 'unknown') {
            $response = @file_get_contents('https://api.ipify.org?format=json');
            if ($response !== false) {
                $data = json_decode($response, true);
                $ip = $data['ip'] ?? 'unknown';
            }
        }
        return json_encode(['ip' => $ip]);
    });

    // -----------------------------------------------------------
    ## Dynamiczne ładowanie tras z bazy danych - tabela cms_routes
    // -----------------------------------------------------------

    try {
        // Pobierz wszystkie aktywne definicje tras z bazy danych
        // Dodaj warunek np. 'is_active' => 1, 'type' => 'web' (jeśli rozróżniasz)
        $dynamicRoutes = RouteModel::where('is_active', 1)->get(); // Używamy Eloquent
    } catch (\Exception $e) {
        // W przypadku błędu połączenia z bazą lub braku tabeli, zaloguj błąd i kontynuuj
        // lub obsłuż go inaczej w środowisku produkcyjnym.
        // echo "Błąd ładowania tras z bazy: " . $e->getMessage();
        $dynamicRoutes = [];
    }

    $webRoutes = [];
    $apiRoutes = [];

    // Pogrupowanie tras na WEB (z AuthMiddleware) i API (z ApiAuthMiddleware)
    foreach ($dynamicRoutes as $route) {
        $data = [
            'method' => strtoupper($route->method), // np. 'GET', 'POST'
            'url' => $route->url,                   // np. 'dashboard', 'users/edit/{id}'
            'controller_action' => $route->controller_name . 'Controller@' . $route->action_name, // np. 'DashboardController@index'
            'name' => $route->name,                 // np. 'dashboard_index'
        ];

        if ($route->group_type === 'web') {
            $webRoutes[] = $data;
        } elseif ($route->group_type === 'api') {
            $apiRoutes[] = $data;
        }
    }

    // -----------------------------------------------------------
    ## Trasy Wymagające Autoryzacji (WEB - używamy AuthMiddleware)
    // -----------------------------------------------------------
    SimpleRouter::group(['middleware' => AuthMiddleware::class], function () use ($webRoutes) {
        // Ładowanie dynamicznych tras WEB
        foreach ($webRoutes as $route) {
            $method = strtolower($route['method']); // 'get', 'post'

            // Używamy dynamicznego wywołania metody SimpleRouter
            if (method_exists(SimpleRouter::class, $method)) {
                SimpleRouter::$method($route['url'], $route['controller_action'])->name($route['name']);
            }
        }

        // Przykład stałej trasy WEB, jeśli jakaś musi być zawsze zdefiniowana lokalnie:
        // SimpleRouter::get('maintenance', 'MaintenanceController@index')->name('maintenance_index');
    });

    // -----------------------------------------------------------
    ## Trasy API/AJAX Wymagające Autoryzacji (używamy ApiAuthMiddleware)
    // -----------------------------------------------------------
    SimpleRouter::group(['prefix' => '/api', 'middleware' => ApiAuthMiddleware::class], function () use ($apiRoutes) {

        // Ładowanie dynamicznych tras API
        foreach ($apiRoutes as $route) {
            $method = strtolower($route['method']);

            if (method_exists(SimpleRouter::class, $method)) {
                SimpleRouter::$method($route['url'], $route['controller_action'])->name($route['name']);
            }
        }

        // Przykład stałej trasy API, jeśli jakaś musi być zawsze zdefiniowana lokalnie:
        // SimpleRouter::get('users/grid', 'UsersController@grid')->name('api_users_grid');
    });
});
