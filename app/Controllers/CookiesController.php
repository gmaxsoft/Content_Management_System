<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;
use \App\Models\Cookies;

/**
 * Cookies Controller
 *
 */
class CookiesController extends DefaultController
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        $settings = Cookies::find(1);

        $cookies_id     = $settings->cookies_id ?? null;
        $cookies_active = $settings->cookies_active ?? 0;
        $cookies_mode   = $settings->cookies_mode ?? '';
        $cookies_txt    = $settings->cookies_txt ?? '';

        if (AuthController::isLoggedIn() === false) {
            View::renderTemplate('Home/index.html');
        } else {
            View::renderTemplate('Cookies/index.html', ['cookies_id' => $cookies_id, 'cookies_active' => $cookies_active, 'cookies_mode' => $cookies_mode, 'cookies_txt' => $cookies_txt]);
        }
    }

    /**
     * Save the settings
     *
     * @return void
     */
    public function update(): void
    {
        # Get all input values
        $post = input()->all();

        $cookies_id = $post['cookies_id'];
        $cookies_active = isset($post['cookies_active']) ? 1 : 0;
        $cookies_mode = $post['cookies_mode'] ?? '';
        $cookies_txt = $post['cookies_txt'];

        try {
            # Próbuj zaktualizować lub stworzyć nowy rekord
            Cookies::updateOrCreate(
                ['cookies_id' => $cookies_id], // Kryteria wyszukiwania
                ['cookies_txt' => $cookies_txt, 'cookies_active' => $cookies_active, 'cookies_mode' => $cookies_mode] // Dane do aktualizacji lub wstawienia
            );

            $this->sendJsonResponse(['success' => true, 'message' => 'Cookies zostały pomyślnie zaktualizowane lub dodane.']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji lub dodawania Cookies: ' . $e->getMessage()], 500);
        }
    }
}
