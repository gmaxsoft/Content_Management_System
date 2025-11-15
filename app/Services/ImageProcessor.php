<?php

namespace App\Services;

use Spatie\Image\Image;
use App\Services\Interfaces\ImageProcessorInterface;

class ImageProcessor implements ImageProcessorInterface
{
    public function processImage(string $sourcePath, string $destinationPath, array $config): bool
    {
        try {
            $image = Image::load($sourcePath);

            // Ustaw rozmiar
            if (!empty($config['width']) && !empty($config['height'])) {
                $image->width($config['width'])->height($config['height']);
            } elseif (!empty($config['width'])) {
                $image->width($config['width']);
            } elseif (!empty($config['height'])) {
                $image->height($config['height']);
            }

            // Ustaw jakość
            if (!empty($config['quality']) && $config['quality'] >= 1 && $config['quality'] <= 100) {
                $image->quality($config['quality']);
            }

            // Konwersja formatu
            if (!empty($config['format']) && $config['format'] !== 'original') {
                $new_extension = strtolower($config['format']);
                $new_destination = preg_replace('/\.[^.]+$/', '.' . $new_extension, $destinationPath);
                $image->save($new_destination);
            } else {
                $image->save($destinationPath);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}