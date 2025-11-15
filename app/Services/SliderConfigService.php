<?php

namespace App\Services;

use App\Models\SliderConfig;
use App\Services\Interfaces\SliderConfigServiceInterface;

class SliderConfigService implements SliderConfigServiceInterface
{
    public function getConfig(): array
    {
        $config = SliderConfig::find(1);

        if (!$config) {
            return [
                'slider_width' => 1920,
                'slider_height' => 1080,
                'slider_quality' => 85,
                'slider_format' => 'webp'
            ];
        }

        return [
            'slider_width' => $config->slider_width,
            'slider_height' => $config->slider_height,
            'slider_quality' => $config->slider_quality,
            'slider_format' => $config->slider_format
        ];
    }

    public function saveConfig(array $config): array
    {
        try {
            SliderConfig::updateOrCreate(
                ['slider_main_id' => $config['slider_id'] ?? 1],
                [
                    'slider_width' => $config['slider_width'] ?? 1920,
                    'slider_height' => $config['slider_height'] ?? 1080,
                    'slider_quality' => $config['slider_quality'] ?? 85,
                    'slider_format' => $config['slider_format'] ?? 'webp'
                ]
            );

            return ['success' => true, 'message' => 'Ustawienia zostały pomyślnie zaktualizowane.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji ustawień: ' . $e->getMessage()];
        }
    }
}