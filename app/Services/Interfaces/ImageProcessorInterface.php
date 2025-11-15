<?php

namespace App\Services\Interfaces;

interface ImageProcessorInterface
{
    public function processImage(string $sourcePath, string $destinationPath, array $config): bool;
}