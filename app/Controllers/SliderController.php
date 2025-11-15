<?php

namespace App\Controllers;

use App\Services\Interfaces\SliderServiceInterface;
use App\Services\Interfaces\SliderFileServiceInterface;
use App\Services\Interfaces\SliderConfigServiceInterface;
use App\Controllers\AuthController;

/**
 * SliderController
 * Refactored to use services for better SOLID compliance
 */
class SliderController extends DefaultController
{
    private ?SliderServiceInterface $sliderService = null;
    private ?SliderFileServiceInterface $sliderFileService = null;
    private ?SliderConfigServiceInterface $sliderConfigService = null;

    public function __construct(
        SliderServiceInterface $sliderService = null,
        SliderFileServiceInterface $sliderFileService = null,
        SliderConfigServiceInterface $sliderConfigService = null,
        ...$parentArgs
    ) {
        $this->sliderService = $sliderService ?? new \App\Services\SliderService();
        $this->sliderFileService = $sliderFileService ?? new \App\Services\SliderFileService(new \App\Services\ImageProcessor());
        $this->sliderConfigService = $sliderConfigService ?? new \App\Services\SliderConfigService();

        // Call parent constructor with remaining arguments
        parent::__construct(...$parentArgs);
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        $current_lang = $_ENV['DEFAULT_BLOCK_LANG'];
        $available_langs = $this->getAvailableLangs($current_lang);

        $config = $this->sliderConfigService->getConfig();

        if (AuthController::isLoggedIn() === false) {
            \Core\View::renderTemplate('Home/index.html');
        } else {
            \Core\View::renderTemplate('Slider/index.html', [
                'available_langs' => $available_langs,
                'current_lang' => $current_lang,
                'slider_width' => $config['slider_width'],
                'slider_height' => $config['slider_height'],
                'slider_quality' => $config['slider_quality'],
                'slider_format' => $config['slider_format']
            ]);
        }
    }

    public function edit($id): void
    {
        $slider = $this->sliderService->getSliderById($id);

        $current_lang = $_ENV['DEFAULT_BLOCK_LANG'];
        $available_langs = $this->getAvailableLangs($current_lang);

        \Core\View::renderTemplate('Slider/edit.html', [
            'slider_id' => $slider->slider_id,
            'slider_identifier' => $slider->slider_identifier,
            'slider_title' => $slider->slider_title,
            'slider_description' => $slider->slider_description,
            'slider_url' => $slider->slider_url,
            'slider_lang' => $slider->slider_lang,
            'slider_display' => $slider->slider_display,
            'available_langs' => $available_langs,
            'current_lang' => $current_lang
        ]);
    }

    public function grid()
    {
        $json = array();
        $results = $this->sliderService->getAllSliders();

        foreach ($results as $row) {
            $slider_id = $row['slider_id'];
            $slider_title = $row['slider_title'];

            $slider_description = strip_tags($row['slider_description'], '');
            $slider_description = $this->substrwords($slider_description, 100);

            $slider_lang = $row->lang ? $row->lang->lang_name : '';
            $slider_display = $row['slider_display'];
            $slider_identifier = $row['slider_identifier'];

            $state = "<input data-index=\"$slider_id\" name=\"btSelectItem\" type=\"checkbox\">";
            $action = "<a class=\"btn btn-info\" href=\"/slider/edit/$slider_id/\"><i class=\"fas fa-pen\"></i></a>";

            $json[] = array(
                'state' => $state,
                'action' => $action,
                'slider_id' => $slider_id,
                'slider_title' => $slider_title,
                'slider_description' => $slider_description,
                'slider_identifier' => $slider_identifier,
                'slider_display' => $slider_display,
                'slider_lang' => $slider_lang
            );
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    /**
     * Dodaje nowy slider do bazy danych.
     * Oczekuje danych slider w POST.
     */

    public function add(): void
    {
        $post = input()->all();

        $data = [
            'slider_identifier' => $post['slider_identifier'],
            'slider_title' => $post['slider_title'],
            'slider_url' => $post['slider_url'],
            'slider_display' => $post['slider_display'],
            'slider_lang' => $post['slider_lang'],
            'slider_description' => $post['slider_description']
        ];

        $result = $this->sliderService->createSlider($data);
        $statusCode = $result['success'] ? 200 : 422;
        $this->sendJsonResponse($result, $statusCode);
    }

    /**
     * Aktualizuje dane slidera w bazie danych.
     * Oczekuje slider_id i innych danych w POST.
     */
    public function update(): void
    {
        $post = input()->all();

        $sliderId = $post['slider_id'];
        $data = [
            'slider_identifier' => $post['slider_identifier'],
            'slider_title' => $post['slider_title'],
            'slider_url' => $post['slider_url'],
            'slider_display' => $post['slider_display'],
            'slider_lang' => $post['slider_lang'],
            'slider_description' => $post['slider_description']
        ];

        $result = $this->sliderService->updateSlider($sliderId, $data);
        $statusCode = $result['success'] ? 200 : 500;
        $this->sendJsonResponse($result, $statusCode);
    }

    /**
     * Usuwa slider z bazy danych.
     * Oczekuje slider_id w danych POST.
     */
    public function remove(): void
    {
        $post = input()->all();
        $sliderId = $post['id'];

        $result = $this->sliderService->deleteSlider($sliderId);
        $statusCode = $result['success'] ? 200 : 422;
        $this->sendJsonResponse($result, $statusCode);
    }

    /**
     * Aktualizuje dane slidera w trybie inline.
     * Oczekuje slider_id i innych danych w POST.
     */
    public function inline(): void
    {
        $post = input()->all();
        $sliderId = $post['pk'];
        $field = $post['name'];
        $value = $post['value'];

        $result = $this->sliderService->updateSliderInline($sliderId, $field, $value);
        if (!$result['success']) {
            $this->sendJsonResponse($result, 422);
        }
    }

    /**
     * Pliki slidera
     */

    public function gridFiles($id = 0)
    {
        $files = $this->sliderFileService->getFilesBySliderId($id);
        $json = array();

        foreach ($files as $row) {
            $file_id = $row['file_id'];
            $file_name = $row['file_name'];
            $file_type = $row['file_type'];
            $file_created_at = $row['file_created_at'];

            $state = '<input data-index="' . $file_id . '" name="btSelectItem" type="checkbox">';
            $pathinfo = pathinfo($file_name);
            $extension = $pathinfo['extension'];
            $imgfile = $file_id . '.' . $extension;
            $upload_dir = $_ENV['SLIDERFILESPATH'];
            $filepath = $upload_dir . $imgfile;

            $file_move = '<span class="move"><i class="fa fa-arrows" aria-hidden="true"></i></span>';
            $file_src = '<a data-fancybox data-type="image" href="/' . $filepath . '" title="Pobierz"><img src="/mthumb.php?src=' . $filepath . '&h=56&w=80&zc=1" alt="Miniatura" /></a>';
            $action = '<a class="btn btn-info" data-fancybox data-type="image" href="/' . $filepath . '" title="Pobierz"><i class="fa-solid fa-magnifying-glass"></i></a> <a class="btn btn-success btn_download_module white" href="/api/slider/downloadfiles/' . $file_id . '/" title="Pobierz"><i class="fas fa-file-download"></i></a>';

            $json[] = array(
                'state' => $state,
                'file_move' => $file_move,
                'file_id' => $file_id,
                'file_src' => $file_src,
                'file_name' => $file_name,
                'file_type' => $file_type,
                'file_created_at' => $file_created_at,
                'action' => $action
            );
        }

        echo json_encode($json);
    }

    public function uploadFiles($file_main_id)
    {
        if (!isset($_FILES['file'])) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Brak pliku do wgrania.'], 400);
            return;
        }

        $result = $this->sliderFileService->uploadFile($file_main_id, $_FILES['file']);
        $statusCode = $result['success'] ? 200 : 500;
        $this->sendJsonResponse($result, $statusCode);
    }

    public function removeFiles()
    {
        $post = $_POST;
        $fileId = $post['id'];

        $result = $this->sliderFileService->deleteFile($fileId);
        $statusCode = $result['success'] ? 200 : 422;
        $this->sendJsonResponse($result, $statusCode);
    }

    public function downloadFiles($file_id)
    {
        $this->sliderFileService->downloadFile($file_id);
    }

    public function orderFiles()
    {
        $post = $_POST;
        $this->sliderFileService->reorderFiles($post['id'], $post['position']);
    }

    public function saveConfig(): void
    {
        $post = input()->all();
        $config = [
            'slider_id' => $post['slider_id'],
            'slider_width' => $post['slider_width'] ?? '',
            'slider_height' => $post['slider_height'] ?? '',
            'slider_quality' => $post['slider_quality'] ?? '85',
            'slider_format' => $post['slider_format']
        ];

        $result = $this->sliderConfigService->saveConfig($config);
        $statusCode = $result['success'] ? 200 : 500;
        $this->sendJsonResponse($result, $statusCode);
    }
}
