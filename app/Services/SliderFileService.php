<?php

namespace App\Services;

use App\Models\SliderFiles;
use App\Services\Interfaces\SliderFileServiceInterface;
use App\Services\Interfaces\ImageProcessorInterface;

class SliderFileService implements SliderFileServiceInterface
{
    private ImageProcessorInterface $imageProcessor;

    public function __construct(ImageProcessorInterface $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
    }

    public function getFilesBySliderId(int $sliderId): array
    {
        return SliderFiles::where('file_main_id', $sliderId)->orderBy('file_position', 'asc')->get()->toArray();
    }

    public function uploadFile(int $sliderId, array $fileData): array
    {
        $file_tmp = $fileData['tmp_name'];
        $file_name = $fileData['name'];
        $file_type = $fileData['type'];
        $file_size = $fileData['size'];

        // Pobierz konfigurację
        $config = $this->getSliderConfig();

        // Oblicz nową pozycję
        $new_position = $this->getNextPosition($sliderId);

        if (empty($file_name)) {
            return ['success' => false, 'message' => 'Brak nazwy pliku!'];
        }

        try {
            $sliderFile = SliderFiles::create([
                'file_name' => $file_name,
                'file_size' => $file_size,
                'file_type' => $file_type,
                'file_main_id' => $sliderId,
                'file_position' => $new_position
            ]);

            $lastid = $sliderFile->file_id;
            $pathinfo = pathinfo($file_name);
            $extension = $pathinfo['extension'];

            $upload_dir = $_ENV['SLIDERFILESPATH'];
            $upload_file = $upload_dir . $lastid . '.' . $extension;

            if (file_exists($file_tmp)) {
                $processed = $this->imageProcessor->processImage($file_tmp, $upload_file, $config);

                if ($processed) {
                    return [
                        'success' => true,
                        'message' => 'Plik został poprawnie wgrany do CRM!',
                        'file_main_id' => $sliderFile->file_main_id,
                        'file_id' => $lastid
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Coś poszło nie tak! Plik nie został wgrany na serwer.'
                    ];
                }
            } else {
                return ['success' => false, 'message' => 'Plik tymczasowy nie istnieje!'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Wystąpił błąd podczas dodawania pliku: ' . $e->getMessage()];
        }
    }

    public function deleteFile(int $fileId): array
    {
        if (empty($fileId) || !is_numeric($fileId)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator pliku.'];
        }

        try {
            $results = SliderFiles::where('file_id', $fileId)->get();

            if ($results->isEmpty()) {
                return ['success' => false, 'error' => 'Plik o podanym ID nie istnieje.'];
            }

            foreach ($results as $row) {
                $file_name = $row['file_name'];
                $pathinfo = pathinfo($file_name);
                $extension = $pathinfo['extension'];
                $upload_dir = $_ENV['SLIDERFILESPATH'];
                $upload_file = $upload_dir . $fileId . '.' . $extension;

                $deletedRows = SliderFiles::where('file_id', $fileId)->delete();

                if ($deletedRows > 0) {
                    if (file_exists($upload_file)) {
                        unlink($upload_file);
                    }
                    return ['success' => true, 'message' => 'Plik został pomyślnie usunięty.'];
                } else {
                    return ['success' => false, 'error' => 'Plik o podanym ID nie istnieje.'];
                }
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas usuwania pliku: ' . $e->getMessage()];
        }

        return ['success' => false, 'error' => 'Nieoczekiwany błąd.'];
    }

    public function downloadFile(int $fileId)
    {
        $results = SliderFiles::where('file_id', $fileId)->get();

        foreach ($results as $row) {
            $filename = $row['file_name'];
            $pathinfo = pathinfo($filename);
            $extension = $pathinfo['extension'];

            $imgfile = $fileId . '.' . $extension;
            $upload_dir = $_ENV['SLIDERFILESPATH'];

            $filepath = $upload_dir . $imgfile;

            if (file_exists($filepath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($filename));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filepath));
                ob_clean();
                flush();
                readfile($filepath);
                exit;
            }
        }
    }

    public function reorderFiles(int $fileId, int $position): void
    {
        SliderFiles::where('file_id', $fileId)->update(['file_position' => $position]);
    }

    private function getNextPosition(int $sliderId): int
    {
        $max_position = SliderFiles::where('file_main_id', $sliderId)->max('file_position');
        return is_null($max_position) ? 1 : $max_position + 1;
    }

    private function getSliderConfig(): array
    {
        // To będzie wstrzyknięte przez konstruktor lub serwis
        return [
            'width' => $_ENV['SLIDER_WIDTH'] ?? 1920,
            'height' => $_ENV['SLIDER_HEIGHT'] ?? 1080,
            'quality' => $_ENV['SLIDER_QUALITY'] ?? 85,
            'format' => $_ENV['SLIDER_FORMAT'] ?? 'webp'
        ];
    }
}