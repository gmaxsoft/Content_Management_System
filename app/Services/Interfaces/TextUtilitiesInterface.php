<?php

namespace App\Services\Interfaces;

interface TextUtilitiesInterface
{
    public function dayOfWeek(): string;
    public function substrwords(string $text, int $maxchar, string $end = '...'): string;
}