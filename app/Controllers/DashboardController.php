<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;
use App\Models\Sentence;

/**
 * Dashboard Controller
 *
 */
class DashboardController extends DefaultController
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        if (AuthController::isLoggedIn() === false) {
            View::renderTemplate('Home/index.html');
        } else {
            View::renderTemplate('Dashboard/index.html', [
                'user_first_name' => $_SESSION['userinfo']['user_first_name'],
                'dayofweek' => $this->dayOfWeek(),
                'sentence_html' => $this->getSentence()
            ]);
        }
    }

    public function getSentence()
    {
        $id = rand(1, 8);
        $results = Sentence::find($id);

        $results->sentence;
        $results->name;

        $html = $results->sentence . ' <strong>' . $results->name . '</strong>';

        return $html;
    }
}
