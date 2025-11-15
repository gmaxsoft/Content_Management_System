<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;
use \App\Models\ServicePassword;

/**
 * Service Password Controller
 *
 */
class ServicePasswordController extends DefaultController
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        if (AuthController::isLoggedIn() === false) {
            View::renderTemplate('Home/index.html');
        } else {
            View::renderTemplate('Servicepassword/index.html');
        }
    }

    /**
     * Show the add page
     *
     * @return void
     */

    public function add(): void
    {
        # Get all input values
        $post = input()->all();
        $errors = [];

        $passwd_hash = $post['passwd_hash'];
        $confirm_passwd_hash = $post['confirm_passwd_hash'];

        # Validate inputs
        if (empty($passwd_hash) || empty($confirm_passwd_hash)) {
            $errors[] = 'Wszystkie pola są wymagane.';
        } elseif ($passwd_hash !== $confirm_passwd_hash) {
            $errors[] = 'Hasła nie są identyczne.';
        } elseif (strlen($passwd_hash) < 8) {
            $errors[] = 'Hasło musi mieć co najmniej 8 znaków.';
        }

        # If there are errors, show them
        if (!empty($errors)) {
            $this->sendJsonResponse(['success' => true, 'errors' => $errors], 422);
        }

        $hashedPassword = password_hash($passwd_hash, PASSWORD_DEFAULT);

        if (ServicePassword::where('passwd_id', 1)->exists()) {
            try {
                $updatedRows = ServicePassword::where('passwd_id', 1)->update(['passwd_hash' => $hashedPassword]);

                if ($updatedRows > 0) {
                    $this->sendJsonResponse(['success' => true, 'message' => 'Hasło zostało pomyślnie zaktualizowane.']);
                } else {
                    $this->sendJsonResponse(['success' => true, 'error' => 'Pole o podanym ID nie istnieje.'], 404);
                }
            } catch (\Exception $e) {
                // Obsługa błędów bazy danych
                $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji hasła: ' . $e->getMessage()], 500);
            }
        } else {
            $result = ServicePassword::firstOrCreate(
                ['passwd_id' => 1],
                ['passwd_hash' => $hashedPassword]
            );

            if ($result->wasRecentlyCreated) {
                $this->sendJsonResponse(['success' => true, 'message' => 'Hasło serwisowe zostało dodane.']);
            } else {
                $this->sendJsonResponse(['success' => true, 'message' => 'Hasło już istnieje, nic nie zmieniam.']);
            }
        }
    }
}
