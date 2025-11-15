<?php

namespace App\Services\Interfaces;

interface SliderConfigServiceInterface
{
    public function getConfig(): array;
    public function saveConfig(array $config): array;
}