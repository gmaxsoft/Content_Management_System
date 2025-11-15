<?php

namespace App\Services\Interfaces;

interface NavigationServiceInterface
{
    public function getNavigationItems(): array;
    public function getNavigationJson(): array;
    public function getNavigationMenu(): string;
    public function storeNavigation(array $data): void;
    public function checkChildExists(string $param, $value): bool;
    public function getChildren(int $parentId): array;
}