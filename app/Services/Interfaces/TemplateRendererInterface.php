<?php

namespace App\Services\Interfaces;

interface TemplateRendererInterface
{
    public function render(string $template, array $data = []): void;
    public function redirect(string $url): void;
}