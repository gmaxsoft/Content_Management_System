<?php

namespace App\Services;

use App\Models\ContactForm;
use App\Services\Interfaces\ContactFormServiceInterface;

class ContactFormService implements ContactFormServiceInterface
{
    public function getContactForm(): ?object
    {
        return ContactForm::find(1);
    }

    public function updateContactForm(array $data): array
    {
        try {
            ContactForm::updateOrCreate(
                ['form_id' => $data['form_id']],
                [
                    'form_email' => $data['form_email'] ?? '',
                    'form_email_alias' => $data['form_email_alias'] ?? '',
                    'form_return_email_status' => $data['form_return_email_status'] ?? 'Nie',
                    'form_return_message' => $data['form_return_message'] ?? ''
                ]
            );

            return ['success' => true, 'message' => 'Ustawienia formularza kontaktowego zostały pomyślnie zaktualizowane.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji ustawień formularza kontaktowego: ' . $e->getMessage()];
        }
    }

    public function getContactFormData(): array
    {
        $form = $this->getContactForm();

        return [
            'form_id' => $form ? $form->form_id : 1,
            'form_email' => $form ? $form->form_email : '',
            'form_email_alias' => $form ? $form->form_email_alias : '',
            'form_return_email_status' => $form ? $form->form_return_email_status : 'Nie',
            'form_return_message' => $form ? $form->form_return_message : ''
        ];
    }
}