<?php

namespace App\Controllers;

use \Core\View;
use \App\Controllers\AuthController;
use \App\Models\Maintenance;

/**
 * Maintenance Controller
 *
 */
class MaintenanceController extends DefaultController
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function index(): void
    {
        $maintenance = Maintenance::find(1); // single maintenance record

        if (AuthController::isLoggedIn() === false) {
            View::renderTemplate('Home/index.html');
        } else {
            View::renderTemplate('Maintenance/index.html', ['maintenance_active' => $maintenance->maintenance_active, 'maintenance_ip' => $maintenance->maintenance_ip, 'maintenance_mode' => $maintenance->maintenance_mode, 'maintenance_txt' => $maintenance->maintenance_txt, 'maintenance_id' => $maintenance->maintenance_id]);
        }
    }
    /**
     * Save the maintenance settings
     *
     * @return void
     */
    public function update(): void
    {
        # Get all input values
        $post = input()->all();

        $maintenance_id = $post['maintenance_id'];
        $maintenance_active = $post['maintenance_active'];
        $maintenance_ip = $post['maintenance_ip'];
        $maintenance_mode = $post['maintenance_mode'];
        $maintenance_txt = $post['maintenance_txt'];

        // 2. Aktualizacja danych w bazie danych
        try {
            Maintenance::where('maintenance_id', $maintenance_id)->update([
                'maintenance_active' => $maintenance_active,
                'maintenance_ip' => $maintenance_ip,
                'maintenance_mode' => $maintenance_mode,
                'maintenance_txt' => $maintenance_txt
            ]);
            $this->sendJsonResponse(['success' => true, 'message' => 'Ustawienia zostały pomyślnie zaktualizowane.']);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['success' => false, 'error' => 'Wystąpił błąd podczas aktualizacji ustawień: ' . $e->getMessage()], 500);
        }
    }
}
