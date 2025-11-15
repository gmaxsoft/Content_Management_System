<?php

namespace App\Services;

use App\Models\NavigationCms as NavigationItem;
use App\Services\Interfaces\NavigationServiceInterface;

class NavigationService implements NavigationServiceInterface
{
    public function getNavigationItems(): array
    {
        return NavigationItem::all()->toArray();
    }

    public function getNavigationJson(): array
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

            if ($this->checkChildExists('child_id', $id)) {
                $json[] = array(
                    'href' => $href,
                    'icon' => $icon,
                    'text' => $text,
                    'target' => $target,
                    'tooltip' => $tooltip,
                    'children' => $this->getChildren($id)
                );
            } else {
                $json[] = array(
                    'href' => $href,
                    'icon' => $icon,
                    'text' => $text,
                    'target' => $target,
                    'tooltip' => $tooltip,
                    "children" => $array
                );
            }
        }

        return $json;
    }

    public function getNavigationMenu(): string
    {
        $json = $this->getNavigationJson();
        return json_encode($json);
    }

    public function storeNavigation(array $data): void
    {
        if (empty($data)) {
            NavigationItem::truncate();
            return;
        }

        NavigationItem::truncate();
        $currentMaxPosition = 0;

        foreach ($data as $itemData) {
            $text = $itemData['text'] ?? '';
            $href = $itemData['href'] ?? '';
            $icon = $itemData['icon'] ?? '';
            $target = $itemData['target'] ?? '';
            $tooltip = $itemData['tooltip'] ?? '';
            $children = $itemData['children'] ?? [];

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

    public function checkChildExists(string $param, $value): bool
    {
        return NavigationItem::where($param, $value)->exists();
    }

    public function getChildren(int $parentId): array
    {
        $children = [];

        if (!empty($parentId)) {
            $childrenResults = NavigationItem::where('child_id', $parentId)->get();

            foreach ($childrenResults as $row) {
                $id = $row['id'];
                $text = $row['text'];
                $href = $row['href'];
                $icon = $row['icon'];
                $target = $row['target'];
                $tooltip = $row['tooltip'];

                if ($this->checkChildExists('child_id', $id)) {
                    $children[] = array(
                        'href' => $href,
                        'icon' => $icon,
                        'text' => $text,
                        'target' => $target,
                        'tooltip' => $tooltip,
                        'children' => $this->getChildren($id)
                    );
                } else {
                    $children[] = array(
                        'href' => $href,
                        'icon' => $icon,
                        'text' => $text,
                        'target' => $target,
                        'tooltip' => $tooltip,
                        "children" => []
                    );
                }
            }
        }

        return $children;
    }

    private function storeChildren(array $childrenData, int $parentId): void
    {
        foreach ($childrenData as $row) {
            $text = $row['text'] ?? '';
            $href = $row['href'] ?? '';
            $icon = $row['icon'] ?? '';
            $target = $row['target'] ?? '';
            $tooltip = $row['tooltip'] ?? '';
            $nestedChildren = $row['children'] ?? [];

            $position = NavigationItem::max('position') + 1;

            $insertData = array(
                'text' => $text,
                'href' => $href,
                'icon' => $icon,
                'target' => $target,
                'tooltip' => $tooltip,
                'child_id' => $parentId,
                'position' => $position
            );

            $newItem = NavigationItem::create($insertData);
            $newlyCreatedId = $newItem->id;

            if (!empty($nestedChildren) && is_array($nestedChildren)) {
                $this->storeChildren($nestedChildren, $newlyCreatedId);
            }
        }
    }
}