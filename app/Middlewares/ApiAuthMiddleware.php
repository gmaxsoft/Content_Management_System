<?php
namespace App\Middlewares;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class ApiAuthMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["userinfo"]['user_id']) || !$_SESSION["userinfo"]['user_id']) {
            // To jest API, więc zawsze zwracamy JSON 401
            header('Content-Type: application/json');
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Unauthorized API access. Please log in.']);
            exit();
        }
    }
}
