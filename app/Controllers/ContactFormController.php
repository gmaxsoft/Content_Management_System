<?php

namespace App\Controllers;

use App\Services\Interfaces\ContactFormServiceInterface;

/**
 * Contact Form Controller
 * Refactored to use services for better SOLID compliance
 */
class ContactFormController extends DefaultController
{
    private ?ContactFormServiceInterface $contactFormService = null;

    public function __construct(
        ContactFormServiceInterface $contactFormService = null,
        ...$parentArgs
    ) {
        $this->contactFormService = $contactFormService ?? new \App\Services\ContactFormService();
// Call parent constructor with remaining arguments
parent::__construct(...$parentArgs);
}

/**
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        $formData = $this->contactFormService->getContactFormData();

        if (\App\Controllers\AuthController::isLoggedIn() === false) {
            \Core\View::renderTemplate('Home/index.html');
        } else {
            \Core\View::renderTemplate('Contactform/index.html', $formData);
        }
    }

    /**
     * Save the settings
     *
     * @return void
     */
    public function update(): void
    {
        $post = input()->all();

        $data = [
            'form_id' => $post['form_id'],
            'form_email' => $post['form_email'],
            'form_email_alias' => $post['form_email_alias'],
            'form_return_email_status' => $post['form_return_email_status'],
            'form_return_message' => $post['form_return_message']
        ];

        $result = $this->contactFormService->updateContactForm($data);
        $statusCode = $result['success'] ? 200 : 500;
        $this->sendJsonResponse($result, $statusCode);
    }
}
