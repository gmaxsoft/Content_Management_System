<?php

namespace App\Services;

use App\Models\Users;
use App\Models\Access;
use App\Services\Interfaces\UserServiceInterface;

class UserService implements UserServiceInterface
{
    public function getUserById(int $id)
    {
        return Users::find($id);
    }

    public function getAllUsers()
    {
        return Users::leftjoin("access", "access.access_level", "=", "users.user_level")
            ->select(
                'users.*',
                'access.access_name'
            )
            ->get();
    }

    public function createUser(array $data): array
    {
        $validation = $this->validateUserData($data);

        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        try {
            $hashedPassword = password_hash($data['user_password'], PASSWORD_DEFAULT);

            $user = Users::create([
                'user_first_name' => $data['user_first_name'],
                'user_last_name' => $data['user_last_name'],
                'user_stand_name' => $data['user_stand_name'] ?? '',
                'user_phone' => $data['user_phone'] ?? '',
                'user_symbol' => $data['user_symbol'],
                'user_email' => $data['user_email'],
                'user_password' => $hashedPassword,
                'user_level' => $data['user_level'] ?? 1,
                'user_active' => 1,
                'user_description' => $data['user_description'] ?? '',
            ]);

            return ['success' => true, 'message' => 'Użytkownik został dodany.', 'user_id' => $user->user_id];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Wystąpił błąd podczas dodawania użytkownika: ' . $e->getMessage()];
        }
    }

    public function updateUser(int $id, array $data): array
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator użytkownika.'];
        }

        $validation = $this->validateUserData($data, true);

        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['errors'][0]];
        }

        // Sprawdź, czy email już istnieje dla innego użytkownika
        if (Users::where('user_email', $data['user_email'])->where('user_id', '<>', $id)->exists()) {
            return ['success' => false, 'error' => 'Ten email jest już zajęty.'];
        }

        try {
            Users::where('user_id', $id)->update([
                'user_first_name' => $data['user_first_name'],
                'user_last_name' => $data['user_last_name'],
                'user_stand_name' => $data['user_stand_name'] ?? '',
                'user_phone' => $data['user_phone'] ?? '',
                'user_symbol' => $data['user_symbol'],
                'user_email' => $data['user_email'],
                'user_description' => $data['user_description'] ?? '',
                'user_level' => $data['user_level'] ?? 1
            ]);

            return ['success' => true, 'message' => 'Dane użytkownika zostały pomyślnie zaktualizowane.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji użytkownika: ' . $e->getMessage()];
        }
    }

    public function deleteUser(int $id): array
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator użytkownika.'];
        }

        try {
            $deletedRows = Users::where('user_id', $id)->delete();

            if ($deletedRows > 0) {
                return ['success' => true, 'message' => 'Użytkownik został pomyślnie usunięty.'];
            } else {
                return ['success' => false, 'error' => 'Użytkownik o podanym ID nie istnieje.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas usuwania użytkownika: ' . $e->getMessage()];
        }
    }

    public function updateUserInline(int $id, string $field, $value): array
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator użytkownika.'];
        }

        try {
            if ($field === 'user_active') {
                $value = ($value === 'Tak' || $value === '1') ? 1 : 0;
            }

            Users::where('user_id', $id)->update([$field => $value]);
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji użytkownika: ' . $e->getMessage()];
        }
    }

    public function updateUserPassword(int $id, string $password, string $confirmPassword): array
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator użytkownika.'];
        }

        $validation = $this->validatePasswordData($password, $confirmPassword);

        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['errors'][0]];
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updatedRows = Users::where('user_id', $id)->update(['user_password' => $hashedPassword]);

            if ($updatedRows > 0) {
                return ['success' => true, 'message' => 'Hasło zostało pomyślnie zaktualizowane.'];
            } else {
                return ['success' => false, 'error' => 'Użytkownik o podanym ID nie istnieje.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji hasła: ' . $e->getMessage()];
        }
    }

    public function validateUserData(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        if (empty($data['user_first_name'])) {
            $errors[] = 'Imię jest wymagane.';
        }

        if (empty($data['user_last_name'])) {
            $errors[] = 'Nazwisko jest wymagane.';
        }

        if (empty($data['user_symbol'])) {
            $errors[] = 'Symbol jest wymagany.';
        }

        if (!empty($data['user_symbol']) && strlen($data['user_symbol']) !== 2) {
            $errors[] = 'Symbol musi mieć 2 znaki.';
        }

        if (empty($data['user_email'])) {
            $errors[] = 'Email jest wymagany.';
        }

        if (!empty($data['user_email']) && !filter_var($data['user_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Niepoprawny format email.';
        }

        if (!$isUpdate) {
            if (empty($data['user_password'])) {
                $errors[] = 'Hasło jest wymagane.';
            }

            if (!empty($data['user_password']) && strlen($data['user_password']) < 6) {
                $errors[] = 'Hasło musi mieć co najmniej 6 znaków.';
            }

            if (!empty($data['user_password']) && !empty($data['user_confirm_password']) && $data['user_password'] !== $data['user_confirm_password']) {
                $errors[] = 'Hasła nie pasują do siebie.';
            }

            // Sprawdź, czy email już istnieje tylko przy tworzeniu nowego użytkownika
            if (!empty($data['user_email']) && Users::where('user_email', $data['user_email'])->exists()) {
                $errors[] = 'Ten email jest już zajęty.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function validatePasswordData(string $password, string $confirmPassword): array
    {
        $errors = [];

        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Hasło musi mieć co najmniej 6 znaków.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Hasła nie pasują do siebie.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function getAccessLevels()
    {
        return Access::all();
    }
}