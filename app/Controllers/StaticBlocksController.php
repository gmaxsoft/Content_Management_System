<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;
use \App\Models\StaticBlocks;

/**
 * Static Blocks Controller
 *
 */
class StaticBlocksController extends DefaultController
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

        if (AuthController::isLoggedIn() === false) {
            View::renderTemplate('Home/index.html');
        } else {
            View::renderTemplate('Staticblocks/index.html', ['available_langs' => $available_langs, 'current_lang' => $current_lang]);
        }
    }

    public function edit($id): void
    {
        // 1. Pobranie bloku statycznego z bazy danych
        $block = StaticBlocks::find($id);

        $current_lang = $_ENV['DEFAULT_BLOCK_LANG'];
        $available_langs = $this->getAvailableLangs($current_lang);

        // 2. Renderowanie widoku edycji bloku statycznego
        View::renderTemplate('Staticblocks/edit.html', ['block_id' => $block->block_id, 'block_identifier' => $block->block_identifier, 'block_title' => $block->block_title, 'block_description' => $block->block_description, 'block_group' => $block->block_group, 'block_lang' => $block->block_lang, 'block_display' => $block->block_display, 'available_langs' => $available_langs, 'current_lang' => $current_lang]);
    }

    public function grid()
    {
        $json = array();
        $results = StaticBlocks::with('lang')->get();

        foreach ($results as $row) {
            $block_id = $row['block_id'];
            $block_title = $row['block_title'];

            $block_description = strip_tags($row['block_description'], '');
            $block_description = $this->substrwords($block_description, 100);

            $block_lang = $row->lang ? $row->lang->lang_name : ''; // Sprawdzenie, czy relacja istnieje
            $block_display = $row['block_display'];
            $block_group = $row['block_group'];
            $block_identifier = $row['block_identifier'];

            $state = "<input data-index=\"$block_id\" name=\"btSelectItem\" type=\"checkbox\">";
            $action = "<a class=\"btn btn-info\" href=\"/staticblocks/edit/$block_id/\"><i class=\"fas fa-pen\"></i></a>";

            $json[] = array('state' => $state, 'action' => $action, 'block_id' => $block_id, 'block_title' => $block_title, 'block_description' => $block_description, 'block_group' => $block_group, 'block_identifier' => $block_identifier, 'block_display' => $block_display, 'block_lang' => $block_lang);
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
        $errors = [];

        # Get all input values
        $post = input()->all();

        // Pojedyncze pola
        $block_title = $post['block_title'];
        $block_description = $post['block_description'];
        $block_lang = $post['block_lang'];
        $block_display = $post['block_display'];
        $block_group = $post['block_group'];
        $block_identifier = $post['block_identifier'];

        if (empty($block_title)) $errors[] = 'Tytuł jest wymagany.';
        if (empty($block_identifier)) $errors[] = 'Identyfikator jest wymagany.';
        if (empty($block_lang)) $errors[] = 'Język jest wymagany.';

        // Sprawdź, czy identyfikator już istnieje
        if (StaticBlocks::where('block_identifier', $block_identifier)->exists()) {
            $errors[] = 'Ten identyfikator jest już zajęty.';
        }

        if (!empty($errors)) {
            $this->sendJsonResponse(['success' => true, 'errors' => $errors], 422);
        }

        // Zapisywanie do bazy danych za pomocą Eloquenta
        try {
            $block = StaticBlocks::create([
                'block_title' => $block_title,
                'block_description' => $block_description,
                'block_lang' => $block_lang,
                'block_display' => $block_display,
                'block_group' => $block_group,
                'block_identifier' => $block_identifier,
            ]);

            $this->sendJsonResponse(['success' => true, 'message' => 'Blok statyczny został dodany.', 'block_id' => $block->block_id]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Wystąpił błąd podczas dodawania bloku statycznego: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Aktualizuje dane blok w bazie danych.
     * Oczekuje user_id i innych danych w POST.
     */
    public function update(): void
    {
        # Get all input values
        $post = input()->all();

        // Pojedyncze pola
        $block_id = $post['block_id'];
        $block_title = $post['block_title'];
        $block_description = $post['block_description'];
        $block_lang = $post['block_lang'];
        $block_display = $post['block_display'];
        $block_group = $post['block_group'];
        $block_identifier = $post['block_identifier'];

        // 1. Walidacja danych
        if (empty($block_id) || !is_numeric($block_id)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Brak lub nieprawidłowy identyfikator bloku.'], 404);
        }
        if (empty($block_identifier)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Identyfikator bloku jest wymagany.'], 422);
        }

        try {
            StaticBlocks::where('block_id', $block_id)->update([
                'block_title' => $block_title,
                'block_description' => $block_description,
                'block_lang' => $block_lang,
                'block_display' => $block_display,
                'block_group' => $block_group,
                'block_identifier' => $block_identifier
            ]);
            $this->sendJsonResponse(['success' => true, 'message' => 'Dane bloku zostały pomyślnie zaktualizowane.']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji bloku: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Usuwa blok z bazy danych.
     * Oczekuje block_id w danych POST.
     */
    public function remove(): void
    {
        # Get all input values
        $post = input()->all();

        $block_id = $post['id'];

        // 2. Walidacja ID
        if (empty($block_id) || !is_numeric($block_id)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Brak lub nieprawidłowy identyfikator bloku.'], 422);
        }

        try {

            $deletedRows = StaticBlocks::where('block_id', $block_id)->delete(); // Usunięcie bloku

            if ($deletedRows > 0) {
                // Jeśli usunięto blok, zwróć sukces
                $this->sendJsonResponse(['success' => true, 'message' => 'Blok statyczny został pomyślnie usunięty.']);
            } else {
                $this->sendJsonResponse(['success' => true, 'error' => 'Blok statyczny o podanym ID nie istnieje.'], 404);
            }
        } catch (\Exception $e) {
            // Obsługa błędów bazy danych
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas usuwania bloku: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Aktualizuje dane blok w trybie inline.
     * Oczekuje block_id i innych danych w POST.
     */
    public function inline(): void
    {
        # Get all input values
        $post = input()->all();

        // Pojedyncze pola
        $block_id = $post['pk']; // ID bloku
        $field = $post['name']; // Nazwa pola do aktualizacji
        $value = $post['value']; // Nowa wartość

        // 1. Walidacja danych
        if (empty($block_id) || !is_numeric($block_id)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Brak lub nieprawidłowy identyfikator bloku.'], 422);
        }

        // 2. Aktualizacja danych w bazie danych
        try {
            StaticBlocks::where('block_id', $block_id)->update([$field => $value]);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji bloku: ' . $e->getMessage()], 500);
        }
    }
}
