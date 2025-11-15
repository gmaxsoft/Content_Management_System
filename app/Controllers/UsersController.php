<?php
namespace App\Controllers;

use \Core\View;
use App\Models\Users;
use App\Models\Access; // Importujemy model dla tabeli 'access'

class UsersController extends DefaultController
{
    /**
     * Wyświetla stronę z listą użytkowników.
     * Używane do renderowania widoku z tabelą użytkowników.
     */

    public function index(): void
    {
        $accessLevels = Access::all();
        View::renderTemplate('Users/index.html', ['accessLevels' => $accessLevels]);
    }

    /**
     * Wyświetla formularz edycji użytkownika.
     * Oczekuje ID użytkownika w URL.
     * Pobiera dane użytkownika z bazy danych i renderuje widok edycji.
     */

    public function edit($id): void
    {
        // 1. Pobranie użytkownika z bazy danych
        $user = Users::find($id);

        // 3. Pobranie poziomów dostępu
        $accessLevels = Access::all();

        // 4. Renderowanie widoku edycji użytkownika
        View::renderTemplate('Users/edit.html', ['user' => $user, 'accessLevels' => $accessLevels]);
    }

    /**
     * Pobiera dane użytkowników w formacie JSON do wyświetlenia w tabeli.
     * Używane przez Bootstrap Table.
     */

    public function grid(): void
    {
        $json = array();

        // Pobieramy wszystkich użytkowników z tabeli 'users' i dołączamy dane
        $data = Users::leftjoin("access", "access.access_level", "=", "users.user_level")
              ->get();

        foreach ($data as $row) {
            $user_id = $row['user_id'];
            $user_first_name = $row['user_first_name'];
            $user_last_name = $row['user_last_name'];
            $user_stand_name = $row['user_stand_name'];
            $user_level = $row['access_name'];
            $user_email = $row['user_email'];
            $user_phone = $row['user_phone'];
            $user_symbol = $row['user_symbol'];
            //$user_active = ($row['user_active'] == '1') ? 'Tak' : 'Nie';
            $user_active = $row['user_active'];

            $state = "<input data-index=\"$user_id\" name=\"btSelectItem\" type=\"checkbox\">";
            $action = "<a class=\"btn btn-info\" href=\"/users/edit/$user_id\"><i class=\"fas fa-pen\"></i></a>";

            $json[] = array('state' => $state, 'action' => $action, 'user_id' => $user_id, 'user_first_name' => $user_first_name, 'user_last_name' => $user_last_name, 'user_stand_name' => $user_stand_name, 'user_level' => $user_level, 'user_email' => $user_email, 'user_phone' => $user_phone, 'user_symbol' => $user_symbol, 'user_active' => $user_active);
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    /**
     * Dodaje nowego użytkownika do bazy danych.
     * Oczekuje danych użytkownika w POST.
     */

    public function add(): void
    {
        # Get all input values
        $post = input()->all();

        // Pojedyncze pola
        $first_name = $post['user_first_name'];
        $last_name = $post['user_last_name'];
        $stand_name = $post['user_stand_name'];
        $phone = $post['user_phone'];
        $symbol = $post['user_symbol'];
        $email = $post['user_email'];
        $description = $post['user_description'] ?? ''; // Opcjonalne pole
        $password = $post['user_password'];
        $confirm_password = $post['user_confirm_password'];
        $user_level = $post['user_level'] ?? 1; // Domyślny poziom użytkownika

        $errors = [];
        if (empty($first_name)) $errors[] = 'Imię jest wymagane.';
        if (empty($last_name)) $errors[] = 'Nazwisko jest wymagane.';
        if (empty($symbol)) $errors[] = 'Symbol jest wymagany.';
        if (strlen($symbol) !== 2) $errors[] = 'Symbol musi mieć 2 znaki.';
        if (empty($email)) $errors[] = 'Email jest wymagany.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Niepoprawny format email.';
        if (empty($password)) $errors[] = 'Hasło jest wymagane.';
        if (strlen($password) < 6) $errors[] = 'Hasło musi mieć co najmniej 6 znaków.';
        if ($password !== $confirm_password) $errors[] = 'Hasła nie pasują do siebie.';

        // Sprawdź, czy email już istnieje
        if (Users::where('user_email', $email)->exists()) {
            $errors[] = 'Ten email jest już zajęty.';
        }

        if (!empty($errors)) {
            $this->sendJsonResponse(['success' => true, 'errors' => $errors], 422);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Zapisywanie do bazy danych za pomocą Eloquenta
        try {
            $user = Users::create([
                'user_first_name' => $first_name,
                'user_last_name' => $last_name,
                'user_stand_name' => $stand_name,
                'user_phone' => $phone,
                'user_symbol' => $symbol,
                'user_email' => $email,
                'user_password' => $hashedPassword,
                'user_level' => $user_level, // Domyślny poziom użytkownika
                'user_active' => 1, // Domyślnie aktywny
                'user_description' => $description,
            ]);

            $this->sendJsonResponse(['success' => true, 'message' => 'Użytkownik został dodany.', 'user_id' => $user->user_id]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Wystąpił błąd podczas dodawania użytkownika: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Aktualizuje dane użytkownika w bazie danych.
     * Oczekuje user_id i innych danych w POST.
     */
    public function update(): void
    {
        # Get all input values
        $post = input()->all();

        // Pojedyncze pola
        $user_id = $post['user_id'];
        $first_name = $post['user_first_name'];
        $last_name = $post['user_last_name'];
        $stand_name = $post['user_stand_name'];
        $phone = $post['user_phone'];
        $symbol = $post['user_symbol'];
        $email = $post['user_email'];
        $description = $post['user_description'] ?? ''; // Opcjonalne pole
        $user_level = $post['user_level'] ?? 1; // Domyślny poziom użytkownika

        // 1. Walidacja danych
        if (empty($user_id) || !is_numeric($user_id)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Brak lub nieprawidłowy identyfikator użytkownika.'], 404);
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Niepoprawny format email.'], 422);
        }

        // Sprawdź, czy email już istnieje dla innego użytkownika
        if (Users::where('user_email', $email)->where('user_id', '<>', $user_id)->exists()) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Ten email jest już zajęty.'], 422);
        }

        // 2. Aktualizacja danych w bazie danych
        try {
            Users::where('user_id', $user_id)->update([
                'user_first_name' => $first_name,
                'user_last_name' => $last_name,
                'user_stand_name' => $stand_name,
                'user_phone' => $phone,
                'user_symbol' => $symbol,
                'user_email' => $email,
                'user_description' => $description,
                'user_level' => $user_level
            ]);
            $this->sendJsonResponse(['success' => true, 'message' => 'Dane użytkownika zostały pomyślnie zaktualizowane.']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji użytkownika: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Aktualizuje hasło użytkownika w bazie danych.
     * Oczekuje user_id, user_password i user_confirm_password w danych POST.
     */
    public function storePassword(): void
    {
        # Get all input values
        $post = input()->all();

        $user_id = $post['user_id'];
        $password = $post['user_password'];
        $confirm_password = $post['user_confirm_password'];

        // 1. Walidacja danych
        if (empty($user_id) || !is_numeric($user_id)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Brak lub nieprawidłowy identyfikator użytkownika.'], 400);
        }

        if (empty($password) || strlen($password) < 6) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Hasło musi mieć co najmniej 6 znaków.'], 422);
            exit();
        }

        if ($password !== $confirm_password) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Hasła nie pasują do siebie.'], 422);
        }

        // 2. Hashowanie hasła
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 3. Aktualizacja hasła w bazie danych
        try {
            $updatedRows = Users::where('user_id', $user_id)->update(['user_password' => $hashedPassword]);

            if ($updatedRows > 0) {
                $this->sendJsonResponse(['success' => true, 'message' => 'Hasło zostało pomyślnie zaktualizowane.']);
            } else {
                $this->sendJsonResponse(['success' => true, 'error' => 'Użytkownik o podanym ID nie istnieje.'], 404);
            }
        } catch (\Exception $e) {
            // Obsługa błędów bazy danych
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji hasła: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Usuwa użytkownika z bazy danych.
     * Oczekuje user_id w danych POST.
     */
    public function remove(): void
    {
        # Get all input values
        $post = input()->all();

        $user_id = $post['id'];

        // 2. Walidacja ID
        if (empty($user_id) || !is_numeric($user_id)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Brak lub nieprawidłowy identyfikator użytkownika.'], 422);
        }

        try {

            $deletedRows = Users::where('user_id', $user_id)->delete(); // Usunięcie użytkownika

            if ($deletedRows > 0) {
                // Jeśli usunięto użytkownika, zwróć sukces
                $this->sendJsonResponse(['success' => true, 'message' => 'Użytkownik został pomyślnie usunięty.']);
            } else {
                $this->sendJsonResponse(['success' => true, 'error' => 'Użytkownik o podanym ID nie istnieje.'], 404);
            }
        } catch (\Exception $e) {
            // Obsługa błędów bazy danych
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas usuwania użytkownika: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Aktualizuje dane użytkownika w trybie inline.
     * Oczekuje user_id i innych danych w POST.
     */
    public function inline(): void
    {
        # Get all input values
        $post = input()->all();

        // Pojedyncze pola
        $user_id = $post['pk']; // ID użytkownika
        $field = $post['name']; // Nazwa pola do aktualizacji
        $value = $post['value']; // Nowa wartość

        // 1. Walidacja danych
        if (empty($user_id) || !is_numeric($user_id)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Brak lub nieprawidłowy identyfikator użytkownika.'], 422);
        }

        // 2. Aktualizacja danych w bazie danych
        try {
            if ($field === 'user_active') {
                $value = ($value === 'Tak') ? 1 : 0; // Konwersja wartości na 1 lub 0
            }
            Users::where('user_id', $user_id)->update([$field => $value]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji użytkownika: ' . $e->getMessage()], 500);
        }
    }
}
