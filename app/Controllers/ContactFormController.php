<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;
use \App\Models\ContactForm;

/**
 * Contact Form Controller
 *
 */
class ContactFormController extends DefaultController
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {

        $form = ContactForm::find(1);

        $form_id = $form ? $form->form_id : 1;
        $form_email = $form ? $form->form_email : '';
        $form_email_alias = $form ? $form->form_email_alias : '';
        $form_return_email_status = $form ? $form->form_return_email_status : 'Nie';
        $form_return_message = $form ? $form->form_return_message : '';

        if (AuthController::isLoggedIn() === false) {
            View::renderTemplate('Home/index.html');
        } else {
            View::renderTemplate('Contactform/index.html', [
                'form_id' => $form_id,
                'form_email' => $form_email,
                'form_email_alias' => $form_email_alias,
                'form_return_email_status' => $form_return_email_status,
                'form_return_message' => $form_return_message
            ]);
        }
    }

    /**
     * Save the settings
     *
     * @return void
     */
    public function update(): void
    {
        # Get all input values
        $post = input()->all();

        $form_id = $post['form_id'];
        $form_email = $post['form_email'];
        $form_email_alias = $post['form_email_alias'];
        $form_return_email_status = $post['form_return_email_status'];
        $form_return_message = $post['form_return_message'];

        try {
            # Próbuj zaktualizować lub stworzyć nowy rekord
            ContactForm::updateOrCreate(
                ['form_id' => $form_id],
                ['form_email' => $form_email, 'form_email_alias' => $form_email_alias, 'form_return_email_status' => $form_return_email_status, 'form_return_message' => $form_return_message] // Dane do aktualizacji lub wstawienia
            );

            $this->sendJsonResponse(['success' => true, 'message' => 'Tag został pomyślnie zaktualizowany lub dodany.']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji lub dodawania Tagu: ' . $e->getMessage()], 500);
        }
    }
}
