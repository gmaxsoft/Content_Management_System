<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;

/**
 * Home controller
 *
 */
class HomeController
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        if (AuthController::isLoggedIn() === false) {
            unset($_COOKIE['token']);
            View::renderTemplate('Home/index.html');
        } else {
            View::renderTemplate('Dashboard/index.html');
        }
    }
}
