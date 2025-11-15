<?php

namespace App\Services\Interfaces;

interface FileUploadServiceInterface
{
    public function uploadFile(array $file, string $destination): array;
    public function validateFile(array $file): bool;
}