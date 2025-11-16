<?php

namespace App\Services;

use App\Services\Interfaces\ImageProcessorInterface;

class ImageProcessor implements ImageProcessorInterface
{
    public function processImage(string $sourcePath, string $destinationPath, array $config): bool
    {
        try {
            // Check if we have GD extension available, otherwise use Intervention Image
            if (extension_loaded('gd')) {
                return $this->processWithGD($sourcePath, $destinationPath, $config);
            } elseif (class_exists('\Spatie\Image\Image')) {
                return $this->processWithSpatie($sourcePath, $destinationPath, $config);
            } else {
                // Fallback - just copy the file without processing
                return copy($sourcePath, $destinationPath);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    private function processWithGD(string $sourcePath, string $destinationPath, array $config): bool
    {
        // Get image info
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $mimeType = $imageInfo['mime'];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Create image resource based on type
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (!$sourceImage) {
            return false;
        }

        // Calculate new dimensions
        $newWidth = $config['width'] ?? $width;
        $newHeight = $config['height'] ?? $height;

        // If both width and height are specified, maintain aspect ratio
        if (!empty($config['width']) && !empty($config['height'])) {
            $aspectRatio = $width / $height;
            $newAspectRatio = $newWidth / $newHeight;

            if ($aspectRatio > $newAspectRatio) {
                // Image is wider than target ratio, fit by width
                $newHeight = $newWidth / $aspectRatio;
            } else {
                // Image is taller than target ratio, fit by height
                $newWidth = $newHeight * $aspectRatio;
            }
        }

        // Create new image
        $newImage = imagecreatetruecolor((int)$newWidth, (int)$newHeight);

        // Preserve transparency for PNG
        if ($mimeType === 'image/png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefill($newImage, 0, 0, $transparent);
        }

        // Resize image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, (int)$newWidth, (int)$newHeight, $width, $height);

        // Determine output format and quality
        $outputFormat = strtolower($config['format'] ?? 'original');
        $quality = $config['quality'] ?? 85;

        // Set destination path with new format if specified
        if ($outputFormat !== 'original' && $outputFormat !== 'jpg') {
            $destinationPath = preg_replace('/\.[^.]+$/', '.' . $outputFormat, $destinationPath);
        }

        // Save image based on format
        $result = false;
        switch ($outputFormat) {
            case 'png':
                $result = imagepng($newImage, $destinationPath, 9); // Max compression for PNG
                break;
            case 'gif':
                $result = imagegif($newImage, $destinationPath);
                break;
            case 'webp':
                $result = imagewebp($newImage, $destinationPath, $quality);
                break;
            case 'jpg':
            case 'jpeg':
            default:
                $result = imagejpeg($newImage, $destinationPath, $quality);
                break;
        }

        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        return $result;
    }

    private function processWithSpatie(string $sourcePath, string $destinationPath, array $config): bool
    {
        try {
            $image = \Spatie\Image\Image::load($sourcePath);

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