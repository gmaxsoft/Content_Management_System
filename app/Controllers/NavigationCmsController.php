<?php

namespace App\Controllers;

use App\Services\Interfaces\NavigationServiceInterface;

class NavigationCmsController extends DefaultController
{
    private ?NavigationServiceInterface $navigationService = null;

    public function __construct(
        NavigationServiceInterface $navigationService = null,
        ...$parentArgs
    ) {
        $this->navigationService = $navigationService ?? new \App\Services\NavigationService();
// Call parent constructor with remaining arguments
parent::__construct(...$parentArgs);
}

/**
    /**
     * Wyświetla stronę z listą poziomów dostępu.
     * Używane do renderowania widoku z tabelą poziomów dostępu.
     */
    public function index(): void
    {
        $navigationItems = $this->navigationService->getNavigationItems();
        \Core\View::renderTemplate('Navigationcms/index.html', ['navigationItems' => $navigationItems]);
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
        return $this->navigationService->checkChildExists($param, $value);
    }

    public function getChildren($id)
    {
        return $this->navigationService->getChildren($id);
    }

    public function getJson(): void
    {
        $json = $this->navigationService->getNavigationJson();
        echo json_encode($json);
    }

    public function getJsonMenu()
    {
        return $this->navigationService->getNavigationMenu();
    }

    public function store(): void
    {
        $json_string = file_get_contents('php://input');
        $json_array = json_decode($json_string, true);

        // Obsługa błędów dekodowania JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Błąd dekodowania JSON: ' . json_last_error_msg());
            return;
        }

        $this->navigationService->storeNavigation($json_array);
    }
    
    // Note: storeChildren functionality is now handled by NavigationService
    // This method is kept for backward compatibility but delegates to the service
}
