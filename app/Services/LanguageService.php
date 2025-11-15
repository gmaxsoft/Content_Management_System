<?php

namespace App\Services;

use App\Models\Lang;
use App\Services\Interfaces\LanguageServiceInterface;

class LanguageService implements LanguageServiceInterface
{
    public function getAvailableLanguages(int $selectedLangId = 0): array
    {
        $languages = Lang::orderBy('created_at', 'asc')->get();
        return $languages->map(function ($lang) use ($selectedLangId) {
            $lang->is_selected = ($lang->lang_id == $selectedLangId);
            return $lang;
        })->toArray();
    }

    public function getCurrentLanguage(): string
    {
        return $_ENV['DEFAULT_BLOCK_LANG'] ?? 'pl';
    }
}