<?php

namespace App\Services;

use App\Services\Interfaces\FileUploadServiceInterface;

class FileUploadService implements FileUploadServiceInterface
{
    public function uploadFile(array $file, string $destination): array
    {
        if (!$this->validateFile($file)) {
            return ['success' => false, 'error' => 'Nieprawidłowy plik.'];
        }

        $targetPath = $destination . '/' . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'path' => $targetPath];
        }

        return ['success' => false, 'error' => 'Błąd podczas uploadowania pliku.'];
    }

    public function validateFile(array $file): bool
    {
        // Sprawdź czy plik został przesłany
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }

        // Sprawdź błędy uploadu
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Sprawdź rozmiar pliku (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            return false;
        }

        // Sprawdź typ MIME (tylko obrazy)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        return true;
    }
}