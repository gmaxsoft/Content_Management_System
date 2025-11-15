<?php

namespace App\Services\Interfaces;

interface ContactFormServiceInterface
{
    public function getContactForm(): ?object;
    public function updateContactForm(array $data): array;
    public function getContactFormData(): array;
}