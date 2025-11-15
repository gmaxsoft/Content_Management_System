<?php

namespace App\Services\Interfaces;

interface UserServiceInterface
{
    public function getUserById(int $id);
    public function getAllUsers();
    public function createUser(array $data): array;
    public function updateUser(int $id, array $data): array;
    public function deleteUser(int $id): array;
    public function updateUserInline(int $id, string $field, $value): array;
    public function updateUserPassword(int $id, string $password, string $confirmPassword): array;
    public function validateUserData(array $data, bool $isUpdate = false): array;
    public function validatePasswordData(string $password, string $confirmPassword): array;
    public function getAccessLevels();
}