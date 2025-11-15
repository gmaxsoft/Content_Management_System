<?php

namespace App\Controllers;

use App\Services\Interfaces\TemplateRendererInterface;
use App\Services\Interfaces\LanguageServiceInterface;
use App\Services\Interfaces\FileUploadServiceInterface;
use App\Services\Interfaces\TextUtilitiesInterface;

class DefaultController
{
    protected ?TemplateRendererInterface $templateRenderer = null;
    protected ?LanguageServiceInterface $languageService = null;
    protected ?FileUploadServiceInterface $fileUploadService = null;
    protected ?TextUtilitiesInterface $textUtilities = null;

    public function __construct(
        TemplateRendererInterface $templateRenderer = null,
        LanguageServiceInterface $languageService = null,
        FileUploadServiceInterface $fileUploadService = null,
        TextUtilitiesInterface $textUtilities = null
    ) {
        $this->templateRenderer = $templateRenderer ?? new \App\Services\TemplateRenderer();
        $this->languageService = $languageService ?? new \App\Services\LanguageService();
        $this->fileUploadService = $fileUploadService ?? new \App\Services\FileUploadService();
        $this->textUtilities = $textUtilities ?? new \App\Services\TextUtilities();
    }

	public function notFound(): void
	{
		$this->templateRenderer->render('404.html');
	}

	public function error500(): void
	{
		$this->templateRenderer->render('500.html');
	}

	public function getAvailableLangs(int $selectedLangId = 0)
	{
		return $this->languageService->getAvailableLanguages($selectedLangId);
	}

	public function filesUpload()
	{
		if (isset($_FILES['upload'])) {
			$result = $this->fileUploadService->uploadFile($_FILES['upload'], 'upload/ckfiles');

			if ($result['success']) {
				$data['file'] = basename($result['path']);
				$data['url'] = '/' . $result['path'];
				$data['uploaded'] = 1;
			} else {
				$data['uploaded'] = 0;
				$data['error']['message'] = $result['error'] ?? 'Error! File not uploaded';
			}
			echo json_encode($data);
		}
	}

	protected function sendJsonResponse($data, $statusCode = 200)
	{
		header('Content-Type: application/json');
		http_response_code($statusCode); // Ustawia status HTTP
		echo json_encode($data);
		exit();
	}

	// Backward compatibility methods
	public static function getInstance()
	{
		return new static();
	}

	// Metody delegowane do serwisów dla kompatybilności wstecznej
	public static function dayOfWeek()
	{
		$textUtils = new \App\Services\TextUtilities();
		return $textUtils->dayOfWeek();
	}

	public function substrwords($text, $maxchar, $end = '...')
	{
		return $this->textUtilities->substrwords($text, $maxchar, $end);
	}
}
