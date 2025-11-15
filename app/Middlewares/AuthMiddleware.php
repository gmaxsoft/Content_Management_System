<?php
namespace App\Middlewares;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter;

class AuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["userinfo"]['user_id']) || !$_SESSION["userinfo"]['user_id']) {
            // Użytkownik NIE jest zalogowany.
            // Ponieważ ten middleware jest przeznaczony dla tras przeglądarkowych (HTML),
            // zawsze przekierowujemy na stronę logowania.
            SimpleRouter::redirect('/login', 302); // Przekierowanie HTTP 302 Found
            exit(); // Ważne!
        }
    }
}
