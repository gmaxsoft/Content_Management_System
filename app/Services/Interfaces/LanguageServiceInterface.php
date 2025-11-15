<?php

namespace App\Services\Interfaces;

interface LanguageServiceInterface
{
    public function getAvailableLanguages(int $selectedLangId = 0): array;
    public function getCurrentLanguage(): string;
}