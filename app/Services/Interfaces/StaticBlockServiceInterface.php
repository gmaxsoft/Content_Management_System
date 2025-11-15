<?php

namespace App\Services\Interfaces;

interface StaticBlockServiceInterface
{
    public function getBlockById(int $id);
    public function getAllBlocks();
    public function createBlock(array $data): array;
    public function updateBlock(int $id, array $data): array;
    public function deleteBlock(int $id): array;
    public function updateBlockInline(int $id, string $field, $value): array;
    public function validateBlockData(array $data, bool $isUpdate = false): array;
}