<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;

/**
 * Home controller
 *
 */
class HomeController
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        // Sprawdź czy użytkownik jest zalogowany przez sesję LUB przez cookie token
        $isLoggedInBySession = isset($_SESSION['userinfo']['user_id']) && $_SESSION['userinfo']['user_id'];
        $isLoggedInByCookie = AuthController::isLoggedIn();
        
        if ($isLoggedInBySession || $isLoggedInByCookie) {
            // Użytkownik jest zalogowany - pokaż dashboard
            View::renderTemplate('Dashboard/index.html', [
                'user_first_name' => $_SESSION['userinfo']['user_first_name'] ?? 'Użytkownik',
                'dayofweek' => $this->dayOfWeek(),
                'sentence_html' => $this->getSentence()
            ]);
        } else {
            // Użytkownik nie jest zalogowany - pokaż formularz logowania
            unset($_COOKIE['token']);
            View::renderTemplate('Home/index.html');
        }
    }
    
    private function dayOfWeek(): string
    {
        $days = [
            'Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 
            'Czwartek', 'Piątek', 'Sobota'
        ];
        $dayIndex = (int)date('w');
        return $days[$dayIndex] ?? '';
    }
    
    private function getSentence(): string
    {
        try {
            $id = rand(1, 8);
            $results = \App\Models\Sentence::find($id);
            if ($results) {
                return $results->sentence . ' <strong>' . $results->name . '</strong>';
            }
        } catch (\Exception $e) {
            // Ignoruj błędy
        }
        return '';
    }
}
