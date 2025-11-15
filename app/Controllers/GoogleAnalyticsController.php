<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;
use \App\Models\GoogleAnalytics;

/**
 * Google AnalyticsController
 *
 */
class GoogleAnalyticsController extends DefaultController
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        $analytics = GoogleAnalytics::find(1);

        $analytics_id = $analytics ? $analytics->analytics_id : 1;
        $analytics_tag = $analytics ? $analytics->analytics_tag : '';

        if (AuthController::isLoggedIn() === false) {
            View::renderTemplate('Home/index.html');
        } else {
            View::renderTemplate('Googleanalytics/index.html', ['analytics_id' => $analytics_id, 'analytics_tag' => $analytics_tag]);
        }
    }

    /**
     * Save the settings
     *
     * @return void
     */
    public function update(): void
    {
        # Get all input values
        $post = input()->all();

        $analytics_id = $post['analytics_id'];
        $analytics_tag = $post['analytics_tag'];

        try {
            # Próbuj zaktualizować lub stworzyć nowy rekord
            GoogleAnalytics::updateOrCreate(
                ['analytics_id' => $analytics_id], // Kryteria wyszukiwania
                ['analytics_tag' => $analytics_tag] // Dane do aktualizacji lub wstawienia
            );

            $this->sendJsonResponse(['success' => true, 'message' => 'Tag został pomyślnie zaktualizowany lub dodany.']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji lub dodawania Tagu: ' . $e->getMessage()], 500);
        }
    }
}
