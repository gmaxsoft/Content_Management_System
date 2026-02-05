<?php
namespace App\Middlewares;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Sprawdź czy użytkownik jest zalogowany przez sesję LUB przez cookie token
        $isLoggedInBySession = isset($_SESSION["userinfo"]['user_id']) && $_SESSION["userinfo"]['user_id'];
        $isLoggedInByCookie = false;
        
        // Sprawdź również przez cookie token (dla przypadków gdy sesja nie jest jeszcze zapisana po przekierowaniu)
        if (isset($_COOKIE['token'])) {
            try {
                $key = $_ENV['SECRET_KEY'] ?? '';
                if (empty($key)) {
                    $key = 'default_secret_key_change_in_production_' . bin2hex(random_bytes(16));
                }
                $decoded = JWT::decode($_COOKIE['token'], new Key($key, 'HS256'));
                $isLoggedInByCookie = true;
                
                // Jeśli cookie jest ważne, ale sesja nie istnieje, odtwórz sesję
                if (!$isLoggedInBySession && isset($decoded->data->user_id)) {
                    // Pobierz dane użytkownika z bazy i ustaw sesję
                    $user = \App\Models\Auth::find($decoded->data->user_id);
                    if ($user) {
                        $_SESSION['userinfo'] = [
                            'user_id' => $user->user_id,
                            'user_first_name' => $user->user_first_name,
                            'user_last_name' => $user->user_last_name,
                            'user_active' => $user->user_active,
                            'user_logged_in' => true,
                            'service_login' => false,
                        ];
                        $isLoggedInBySession = true;
                    }
                }
            } catch (\Exception $e) {
                // Token nieprawidłowy lub wygasły
                $isLoggedInByCookie = false;
            }
        }

        if (!$isLoggedInBySession && !$isLoggedInByCookie) {
            // Użytkownik NIE jest zalogowany.
            // Ponieważ ten middleware jest przeznaczony dla tras przeglądarkowych (HTML),
            // zawsze przekierowujemy na stronę logowania.
            redirect('/login'); // Przekierowanie HTTP 302 Found
            exit(); // Ważne!
        }
    }
}
