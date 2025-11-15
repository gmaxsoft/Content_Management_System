<?php
namespace App\Controllers;

use App\Services\Interfaces\UserServiceInterface;

class UsersController extends DefaultController
{
    private ?UserServiceInterface $userService = null;

    public function __construct(
        UserServiceInterface $userService = null,
        ...$parentArgs
    ) {
        $this->userService = $userService ?? new \App\Services\UserService();

        // Call parent constructor with remaining arguments
        parent::__construct(...$parentArgs);
    }

    /**
     * Wyświetla stronę z listą użytkowników.
     * Używane do renderowania widoku z tabelą użytkowników.
     */
    public function index(): void
    {
        $accessLevels = $this->userService->getAccessLevels();
        \Core\View::renderTemplate('Users/index.html', ['accessLevels' => $accessLevels]);
    }

    /**
     * Wyświetla formularz edycji użytkownika.
     * Oczekuje ID użytkownika w URL.
     * Pobiera dane użytkownika z bazy danych i renderuje widok edycji.
     */
    public function edit($id): void
    {
        $user = $this->userService->getUserById($id);
        $accessLevels = $this->userService->getAccessLevels();

        \Core\View::renderTemplate('Users/edit.html', ['user' => $user, 'accessLevels' => $accessLevels]);
    }

    /**
     * Pobiera dane użytkowników w formacie JSON do wyświetlenia w tabeli.
     * Używane przez Bootstrap Table.
     */
    public function grid(): void
    {
        $json = array();
        $data = $this->userService->getAllUsers();

        foreach ($data as $row) {
            $user_id = $row['user_id'];
            $user_first_name = $row['user_first_name'];
            $user_last_name = $row['user_last_name'];
            $user_stand_name = $row['user_stand_name'];
            $user_level = $row['access_name'];
            $user_email = $row['user_email'];
            $user_phone = $row['user_phone'];
            $user_symbol = $row['user_symbol'];
            $user_active = $row['user_active'];

            $state = "<input data-index=\"$user_id\" name=\"btSelectItem\" type=\"checkbox\">";
            $action = "<a class=\"btn btn-info\" href=\"/users/edit/$user_id\"><i class=\"fas fa-pen\"></i></a>";

            $json[] = array(
                'state' => $state,
                'action' => $action,
                'user_id' => $user_id,
                'user_first_name' => $user_first_name,
                'user_last_name' => $user_last_name,
                'user_stand_name' => $user_stand_name,
                'user_level' => $user_level,
                'user_email' => $user_email,
                'user_phone' => $user_phone,
                'user_symbol' => $user_symbol,
                'user_active' => $user_active
            );
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
        $post = input()->all();

        $data = [
            'user_first_name' => $post['user_first_name'],
            'user_last_name' => $post['user_last_name'],
            'user_stand_name' => $post['user_stand_name'] ?? '',
            'user_phone' => $post['user_phone'] ?? '',
            'user_symbol' => $post['user_symbol'],
            'user_email' => $post['user_email'],
            'user_description' => $post['user_description'] ?? '',
            'user_password' => $post['user_password'],
            'user_confirm_password' => $post['user_confirm_password'],
            'user_level' => $post['user_level'] ?? 1
        ];

        $result = $this->userService->createUser($data);

        if (!$result['success']) {
            $statusCode = isset($result['errors']) ? 422 : 500;
            $this->sendJsonResponse($result, $statusCode);
        } else {
            $this->sendJsonResponse($result, 200);
        }
    }

    /**
     * Aktualizuje dane użytkownika w bazie danych.
     * Oczekuje user_id i innych danych w POST.
     */
    public function update(): void
    {
        $post = input()->all();

        $userId = $post['user_id'];
        $data = [
            'user_first_name' => $post['user_first_name'],
            'user_last_name' => $post['user_last_name'],
            'user_stand_name' => $post['user_stand_name'] ?? '',
            'user_phone' => $post['user_phone'] ?? '',
            'user_symbol' => $post['user_symbol'],
            'user_email' => $post['user_email'],
            'user_description' => $post['user_description'] ?? '',
            'user_level' => $post['user_level'] ?? 1
        ];

        $result = $this->userService->updateUser($userId, $data);
        $statusCode = $result['success'] ? 200 : 422;
        $this->sendJsonResponse($result, $statusCode);
    }

    /**
     * Aktualizuje hasło użytkownika w bazie danych.
     * Oczekuje user_id, user_password i user_confirm_password w danych POST.
     */
    public function storePassword(): void
    {
        $post = input()->all();

        $userId = $post['user_id'];
        $password = $post['user_password'];
        $confirmPassword = $post['user_confirm_password'];

        $result = $this->userService->updateUserPassword($userId, $password, $confirmPassword);
        $statusCode = $result['success'] ? 200 : 422;
        $this->sendJsonResponse($result, $statusCode);
    }

    /**
     * Usuwa użytkownika z bazy danych.
     * Oczekuje user_id w danych POST.
     */
    public function remove(): void
    {
        $post = input()->all();
        $userId = $post['id'];

        $result = $this->userService->deleteUser($userId);
        $statusCode = $result['success'] ? 200 : 422;
        $this->sendJsonResponse($result, $statusCode);
    }

    /**
     * Aktualizuje dane użytkownika w trybie inline.
     * Oczekuje user_id i innych danych w POST.
     */
    public function inline(): void
    {
        $post = input()->all();
        $userId = $post['pk'];
        $field = $post['name'];
        $value = $post['value'];

        $result = $this->userService->updateUserInline($userId, $field, $value);
        if (!$result['success']) {
            $this->sendJsonResponse($result, 422);
        }
    }
}
