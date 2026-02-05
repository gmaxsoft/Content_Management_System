<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Navigation as NavigationItem;

class NavigationController extends DefaultController
{
    /**
     * Wyświetla stronę z listą poziomów dostępu.
     * Używane do renderowania widoku z tabelą poziomów dostępu.
     */

    public function index(): void
    {
        $navigationItems = NavigationItem::all();
        View::renderTemplate('Navigation/index.html', ['navigationItems' => $navigationItems]);
    }

    /**
     * Sprawdza istnienie rekordu w tabeli 'navigation_cms' na podstawie podanego parametru i wartości.
     * Używa Query Buildera do zabezpieczenia przed SQL injection.
     *
     * @param string $param Nazwa kolumny, którą chcemy sprawdzić.
     * @param mixed $value Wartość, której szukamy w kolumnie.
     * @return bool Zwraca true, jeśli rekord istnieje, w przeciwnym razie false.
     */
    public function checkQueryBuilder($param, $value)
    {
        $exists = NavigationItem::where($param, $value)
            ->exists();

        return $exists;
    }

    public function getChildren($id)
    {
        $array = array();
        $json = array();

        if (!empty($id)) {
            $children_results = NavigationItem::where('child_id', $id)->get();

            foreach ($children_results as $row) {

                $id = $row['id'];
                $text = $row['text'];
                $href = $row['href'];
                $icon = $row['icon'];
                $target = $row['target'];
                $tooltip = $row['tooltip'];
                $child_id = $row['child_id'];

                if ($this->checkQueryBuilder('child_id', $id))
                    $json[] = array('href' => $href, 'icon' => $icon, 'text' => $text, 'target' => $target, 'tooltip' => $tooltip, 'children' => $this->getChildren($id));
                else
                    $json[] = array('href' => $href, 'icon' => $icon, 'text' => $text, 'target' => $target, 'tooltip' => $tooltip, "children" => $array);
            }

            return $json;
        }
        
        return $json;
    }

    public function getJson(): void
    {
        $json = array();
        $array = array();

        // Pobiera wszystkie elementy z tabeli 'navigation_cms' gdzie 'child_id' jest równe 0
        $results = NavigationItem::where('child_id', 0)->get();

        foreach ($results as $row) {

            $id = $row['id'];
            $text = $row['text'];
            $href = $row['href'];
            $icon = $row['icon'];
            $target = $row['target'];
            $tooltip = $row['tooltip'];
            $child_id = $row['child_id'];

            if ($this->checkQueryBuilder('child_id', $id))
                $json[] = array('href' => $href, 'icon' => $icon, 'text' => $text, 'target' => $target, 'tooltip' => $tooltip, 'children' => $this->getChildren($id));
            else
                $json[] = array('href' => $href, 'icon' => $icon, 'text' => $text, 'target' => $target, 'tooltip' => $tooltip, "children" => $array);
        }

        echo json_encode($json);
    }

    public function getJsonMenu()
    {
        $json = array();
        $array = array();

        $results = NavigationItem::where('child_id', 0)->get();

        foreach ($results as $row) {

            $id = $row['id'];
            $text = $row['text'];
            $href = $row['href'];
            $icon = $row['icon'];
            $target = $row['target'];
            $tooltip = $row['tooltip'];
            $child_id = $row['child_id'];

            if ($this->checkQueryBuilder('child_id', $id))
                $json[] = array('href' => $href, 'icon' => $icon, 'text' => $text, 'target' => $target, 'tooltip' => $tooltip, 'children' => $this->getChildren($id));
            else
                $json[] = array('href' => $href, 'icon' => $icon, 'text' => $text, 'target' => $target, 'tooltip' => $tooltip, "children" => $array);
        }

        return json_encode($json);
    }

    public function store(): void
    {
        $json_string = file_get_contents('php://input');
        $json_array = json_decode($json_string, true);

        // Obsługa błędów dekodowania JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Możesz tutaj zalogować błąd lub zwrócić odpowiedź z błędem
            error_log('Błąd dekodowania JSON: ' . json_last_error_msg());
            // Można też zwrócić odpowiedź HTTP z błędem, np. response()->json(['error' => 'Invalid JSON'], 400);
            return;
        }

        if (empty($json_array)) {
            NavigationItem::truncate();
            return; 
        }

        NavigationItem::truncate();

        $currentMaxPosition = NavigationItem::max('position') ?? 0;

        foreach ($json_array as $data) {
            $text = $data['text'] ?? '';
            $href = $data['href'] ?? '';
            $icon = $data['icon'] ?? '';
            $target = $data['target'] ?? '';
            $tooltip = $data['tooltip'] ?? '';
            $children = $data['children'] ?? [];

            // Inkrementuj pozycję dla każdego głównego elementu
            $currentMaxPosition++;

            $newItem = NavigationItem::create([
                'text' => $text,
                'href' => $href,
                'icon' => $icon,
                'target' => $target,
                'tooltip' => $tooltip,
                'child_id' => 0,
                'position' => $currentMaxPosition
            ]);

            $newlyCreatedId = $newItem->id;

            if (!empty($children) && is_array($children)) {
                $this->storeChildren($children, $newlyCreatedId);
            }
        }
    }
    
    public function storeChildren($childrenData = array(), $parentId = 0)
    {
        foreach ($childrenData as $row) {
            $text = $row['text'] ?? '';
            $href = $row['href'] ?? '';
            $icon = $row['icon'] ?? '';
            $target = $row['target'] ?? '';
            $tooltip = $row['tooltip'] ?? '';
            $nestedChildren = $row['children'] ?? [];
            $position = NavigationItem::max('position') + 1;

            $insert_data = array(
                'text' => $text,
                'href' => $href,
                'icon' => $icon,
                'target' => $target,
                'tooltip' => $tooltip,
                'child_id' => $parentId,
                'position' => $position
            );

            $newItem = NavigationItem::create($insert_data);
            $newlyCreatedId = $newItem->id;

            if (!empty($nestedChildren) && is_array($nestedChildren)) {
                $this->storeChildren($nestedChildren, $newlyCreatedId);
            }
        }
    }
}
