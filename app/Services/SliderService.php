<?php

namespace App\Services;

use App\Models\Slider;
use App\Services\Interfaces\SliderServiceInterface;

class SliderService implements SliderServiceInterface
{
    public function getSliderById(int $id)
    {
        return Slider::find($id);
    }

    public function getAllSliders()
    {
        return Slider::with('lang')->get();
    }

    public function createSlider(array $data): array
    {
        $errors = [];

        if (empty($data['slider_title'])) $errors[] = 'Tytuł jest wymagany.';
        if (empty($data['slider_identifier'])) $errors[] = 'Identyfikator jest wymagany.';

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $slider = Slider::create([
                'slider_identifier' => $data['slider_identifier'],
                'slider_title' => $data['slider_title'],
                'slider_url' => $data['slider_url'] ?? '',
                'slider_display' => $data['slider_display'] ?? 0,
                'slider_lang' => $data['slider_lang'] ?? '',
                'slider_description' => $data['slider_description'] ?? '',
                'slider_position' => 0
            ]);

            return ['success' => true, 'message' => 'Slider został dodany.', 'slider_id' => $slider->slider_id];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Wystąpił błąd podczas dodawania slidera: ' . $e->getMessage()];
        }
    }

    public function updateSlider(int $id, array $data): array
    {
        if (empty($id) || !is_numeric($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator slidera.'];
        }

        try {
            Slider::where('slider_id', $id)->update([
                'slider_identifier' => $data['slider_identifier'],
                'slider_title' => $data['slider_title'],
                'slider_url' => $data['slider_url'],
                'slider_display' => $data['slider_display'],
                'slider_lang' => $data['slider_lang'],
                'slider_description' => $data['slider_description']
            ]);
            return ['success' => true, 'message' => 'Dane slidera zostały pomyślnie zaktualizowane.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji slidera: ' . $e->getMessage()];
        }
    }

    public function deleteSlider(int $id): array
    {
        if (empty($id) || !is_numeric($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator slidera.'];
        }

        try {
            $deletedRows = Slider::where('slider_id', $id)->delete();

            if ($deletedRows > 0) {
                return ['success' => true, 'message' => 'Slider został pomyślnie usunięty.'];
            } else {
                return ['success' => false, 'error' => 'Slider o podanym ID nie istnieje.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas usuwania slidera: ' . $e->getMessage()];
        }
    }

    public function updateSliderInline(int $id, string $field, $value): array
    {
        if (empty($id) || !is_numeric($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator slidera.'];
        }

        try {
            Slider::where('slider_id', $id)->update([$field => $value]);
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji slidera: ' . $e->getMessage()];
        }
    }
}