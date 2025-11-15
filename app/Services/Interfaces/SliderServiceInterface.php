<?php

namespace App\Services\Interfaces;

interface SliderServiceInterface
{
    public function getSliderById(int $id);
    public function getAllSliders();
    public function createSlider(array $data): array;
    public function updateSlider(int $id, array $data): array;
    public function deleteSlider(int $id): array;
    public function updateSliderInline(int $id, string $field, $value): array;
}