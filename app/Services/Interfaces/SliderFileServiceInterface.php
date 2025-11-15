<?php

namespace App\Services\Interfaces;

interface SliderFileServiceInterface
{
    public function getFilesBySliderId(int $sliderId): array;
    public function uploadFile(int $sliderId, array $fileData): array;
    public function deleteFile(int $fileId): array;
    public function downloadFile(int $fileId);
    public function reorderFiles(int $fileId, int $position): void;
}