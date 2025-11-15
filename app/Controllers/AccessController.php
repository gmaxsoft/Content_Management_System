<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Access;

class AccessController extends DefaultController
{
    /**
     * Wyświetla stronę z listą poziomów dostępu.
     * Używane do renderowania widoku z tabelą poziomów dostępu.
     */

    public function index(): void
    {
        $accessLevels = Access::all();
        $js_file = 'App/Views/Access/index.js';

        View::renderTemplate('Access/index.html', ['accessLevels' => $accessLevels, 'js_file' => $js_file]);
    }

    public function edit($id): void
    {
        // 1. Pobranie poziomu dostępu z bazy danych
        $access = Access::find($id);
        if (!$access) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Poziom dostępu o podanym ID nie istnieje.'], 404);
        }
        // 2. Renderowanie widoku edycji poziomu dostępu
        View::renderTemplate('Access/edit.html', ['access' => $access]);
    }

    public function grid(): void
    {
        $json = array();
        // Pobieramy wszystkie poziomy dostępu z tabeli 'access'
        $data = Access::all();

        foreach ($data as $row) {
            $access_id = $row['access_id'];
            $access_name = $row['access_name'];
            $access_level = $row['access_level'];
            $access_description = $row['access_description'];

            $state = "<input data-index=\"$access_id\" name=\"btSelectItem\" type=\"checkbox\">";
            $action = "<a class=\"btn btn-info\" href=\"/access/edit/$access_id\"><i class=\"fas fa-pen\"></i></a>";

            $json[] = array('state' => $state, 'action' => $action, 'access_id' => $access_id, 'access_name' => $access_name, 'access_level' => $access_level, 'access_description' => $access_description);
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    public function remove(): void
    {
        $post = input()->all();

        $access_id = $post['id'];

        // Walidacja ID
        if (empty($access_id) || !is_numeric($access_id)) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Brak lub nieprawidłowy identyfikator poziomu dostępu.'], 422);
        }

        try {

            $deletedRows = Access::where('access_id', $access_id)->delete(); // Usunięcie poziomu dostępu

            if ($deletedRows > 0) {
                $this->sendJsonResponse(['success' => true, 'message' => 'Poziom dostępu został pomyślnie usunięty.']);
            } else {
                $this->sendJsonResponse(['success' => true, 'error' => 'Poziom dostępu o podanym ID nie istnieje.'], 404);
            }
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas usuwania poziomu dostępu: ' . $e->getMessage()], 500);
        }
    }

    public function add(): void
    {
        $post = input()->all();

        // Walidacja danych wejściowych
        if (empty($post['access_name']) || empty($post['access_level'])) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Pola "Nazwa uprawnienia" i "Poziom dostępu" są wymagane.'], 422);
        }

        try {

            Access::create([
                'access_name' => $post['access_name'],
                'access_level' => $post['access_level'],
                'access_description' => $post['access_description'] ?? ''
            ]);

            $this->sendJsonResponse(['success' => true, 'message' => 'Poziom dostępu został pomyślnie zaktualizowany.']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji poziomu dostępu: ' . $e->getMessage()], 500);
        }
    }

    public function update(): void
    {
        $post = input()->all();

        // Walidacja danych wejściowych
        if (empty($post['access_name']) || empty($post['access_level'])) {
            $this->sendJsonResponse(['success' => true, 'error' => 'Pola "Nazwa uprawnienia" i "Poziom dostępu" są wymagane.'], 422);
        }

        try {
            Access::where('access_id', $post['access_id'])->update([
                'access_name' => $post['access_name'],
                'access_level' => $post['access_level'],
                'access_description' => $post['access_description'] ?? ''
            ]);

            $this->sendJsonResponse(['success' => true, 'message' => 'Poziom dostępu został pomyślnie zaktualizowany.']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji poziomu dostępu: ' . $e->getMessage()], 500);
        }
    }
}
