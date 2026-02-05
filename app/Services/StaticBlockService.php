<?php

namespace App\Services;

use App\Models\StaticBlocks;
use App\Services\Interfaces\StaticBlockServiceInterface;

class StaticBlockService implements StaticBlockServiceInterface
{
    public function getBlockById(int $id)
    {
        return StaticBlocks::find($id);
    }

    public function getAllBlocks()
    {
        return StaticBlocks::with('lang')->get();
    }

    public function createBlock(array $data): array
    {
        $validation = $this->validateBlockData($data);

        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }

        try {
            $block = StaticBlocks::create([
                'block_title' => $data['block_title'],
                'block_description' => $data['block_description'],
                'block_lang' => $data['block_lang'],
                'block_display' => $data['block_display'] ?? 0,
                'block_group' => $data['block_group'] ?? '',
                'block_identifier' => $data['block_identifier'],
            ]);

            return ['success' => true, 'message' => 'Blok statyczny został dodany.', 'block_id' => $block->block_id];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Wystąpił błąd podczas dodawania bloku statycznego: ' . $e->getMessage()];
        }
    }

    public function updateBlock(int $id, array $data): array
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator bloku.'];
        }

        $validation = $this->validateBlockData($data, true);

        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['errors'][0]];
        }

        try {
            StaticBlocks::where('block_id', $id)->update([
                'block_title' => $data['block_title'],
                'block_description' => $data['block_description'],
                'block_lang' => $data['block_lang'],
                'block_display' => $data['block_display'],
                'block_group' => $data['block_group'],
                'block_identifier' => $data['block_identifier']
            ]);
            return ['success' => true, 'message' => 'Dane bloku zostały pomyślnie zaktualizowane.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji bloku: ' . $e->getMessage()];
        }
    }

    public function deleteBlock(int $id): array
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator bloku.'];
        }

        try {
            $deletedRows = StaticBlocks::where('block_id', $id)->delete();

            if ($deletedRows > 0) {
                return ['success' => true, 'message' => 'Blok statyczny został pomyślnie usunięty.'];
            } else {
                return ['success' => false, 'error' => 'Blok statyczny o podanym ID nie istnieje.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas usuwania bloku: ' . $e->getMessage()];
        }
    }

    public function updateBlockInline(int $id, string $field, $value): array
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'Brak lub nieprawidłowy identyfikator bloku.'];
        }

        try {
            StaticBlocks::where('block_id', $id)->update([$field => $value]);
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji bloku: ' . $e->getMessage()];
        }
    }

    public function validateBlockData(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        if (empty($data['block_title'])) {
            $errors[] = 'Tytuł jest wymagany.';
        }

        if (empty($data['block_identifier'])) {
            $errors[] = 'Identyfikator jest wymagany.';
        }

        if (empty($data['block_lang'])) {
            $errors[] = 'Język jest wymagany.';
        }

        // Sprawdź unikalność identyfikatora tylko przy tworzeniu nowego bloku
        if (!$isUpdate && !empty($data['block_identifier'])) {
            if (StaticBlocks::where('block_identifier', $data['block_identifier'])->exists()) {
                $errors[] = 'Ten identyfikator jest już zajęty.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}