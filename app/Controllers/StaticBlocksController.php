<?php

namespace App\Controllers;

use App\Services\Interfaces\StaticBlockServiceInterface;

/**
 * Static Blocks Controller
 * Refactored to use services for better SOLID compliance
 */
class StaticBlocksController extends DefaultController
{
    private ?StaticBlockServiceInterface $staticBlockService = null;

    public function __construct(
        StaticBlockServiceInterface $staticBlockService = null,
        ...$parentArgs
    ) {
        $this->staticBlockService = $staticBlockService ?? new \App\Services\StaticBlockService();

        // Call parent constructor with remaining arguments
        parent::__construct(...$parentArgs);
    }

{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        $current_lang = $_ENV['DEFAULT_BLOCK_LANG'];
        $available_langs = $this->getAvailableLangs($current_lang);

        if (\App\Controllers\AuthController::isLoggedIn() === false) {
            \Core\View::renderTemplate('Home/index.html');
        } else {
            \Core\View::renderTemplate('Staticblocks/index.html', ['available_langs' => $available_langs, 'current_lang' => $current_lang]);
        }
    }

    public function edit($id): void
    {
        $block = $this->staticBlockService->getBlockById($id);

        $current_lang = $_ENV['DEFAULT_BLOCK_LANG'];
        $available_langs = $this->getAvailableLangs($current_lang);

        \Core\View::renderTemplate('Staticblocks/edit.html', [
            'block_id' => $block->block_id,
            'block_identifier' => $block->block_identifier,
            'block_title' => $block->block_title,
            'block_description' => $block->block_description,
            'block_group' => $block->block_group,
            'block_lang' => $block->block_lang,
            'block_display' => $block->block_display,
            'available_langs' => $available_langs,
            'current_lang' => $current_lang
        ]);
    }

    public function grid()
    {
        $json = array();
        $results = $this->staticBlockService->getAllBlocks();

        foreach ($results as $row) {
            $block_id = $row['block_id'];
            $block_title = $row['block_title'];

            $block_description = strip_tags($row['block_description'], '');
            $block_description = $this->substrwords($block_description, 100);

            $block_lang = $row->lang ? $row->lang->lang_name : '';
            $block_display = $row['block_display'];
            $block_group = $row['block_group'];
            $block_identifier = $row['block_identifier'];

            $state = "<input data-index=\"$block_id\" name=\"btSelectItem\" type=\"checkbox\">";
            $action = "<a class=\"btn btn-info\" href=\"/staticblocks/edit/$block_id/\"><i class=\"fas fa-pen\"></i></a>";

            $json[] = array(
                'state' => $state,
                'action' => $action,
                'block_id' => $block_id,
                'block_title' => $block_title,
                'block_description' => $block_description,
                'block_group' => $block_group,
                'block_identifier' => $block_identifier,
                'block_display' => $block_display,
                'block_lang' => $block_lang
            );
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    /**
     * Dodaje nowego blok do bazy danych.
     * Oczekuje danych blok w POST.
     */

    public function add(): void
    {
        $post = input()->all();

        $data = [
            'block_title' => $post['block_title'],
            'block_description' => $post['block_description'],
            'block_lang' => $post['block_lang'],
            'block_display' => $post['block_display'] ?? 0,
            'block_group' => $post['block_group'] ?? '',
            'block_identifier' => $post['block_identifier']
        ];

        $result = $this->staticBlockService->createBlock($data);

        if (!$result['success']) {
            $statusCode = isset($result['errors']) ? 422 : 500;
            $this->sendJsonResponse($result, $statusCode);
        } else {
            $this->sendJsonResponse($result, 200);
        }
    }

    /**
     * Aktualizuje dane blok w bazie danych.
     * Oczekuje user_id i innych danych w POST.
     */
    public function update(): void
    {
        $post = input()->all();

        $blockId = $post['block_id'];
        $data = [
            'block_title' => $post['block_title'],
            'block_description' => $post['block_description'],
            'block_lang' => $post['block_lang'],
            'block_display' => $post['block_display'],
            'block_group' => $post['block_group'],
            'block_identifier' => $post['block_identifier']
        ];

        $result = $this->staticBlockService->updateBlock($blockId, $data);
        $statusCode = $result['success'] ? 200 : 422;
        $this->sendJsonResponse($result, $statusCode);
    }

    /**
     * Usuwa blok z bazy danych.
     * Oczekuje block_id w danych POST.
     */
    public function remove(): void
    {
        $post = input()->all();
        $blockId = $post['id'];

        $result = $this->staticBlockService->deleteBlock($blockId);
        $statusCode = $result['success'] ? 200 : 422;
        $this->sendJsonResponse($result, $statusCode);
    }

    /**
     * Aktualizuje dane blok w trybie inline.
     * Oczekuje block_id i innych danych w POST.
     */
    public function inline(): void
    {
        $post = input()->all();
        $blockId = $post['pk'];
        $field = $post['name'];
        $value = $post['value'];

        $result = $this->staticBlockService->updateBlockInline($blockId, $field, $value);
        if (!$result['success']) {
            $this->sendJsonResponse($result, 422);
        }
    }
}
