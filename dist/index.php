<?php
require dirname(__DIR__)  . '/vendor/autoload.php';
require dirname(__DIR__)  . '/app/helpers.php';
require dirname(__DIR__)  . '/config/routes.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

set_error_handler('Core\ErrorHandler::handleError', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
set_exception_handler('Core\ErrorHandler::handleException');

use Core\Database;
Database::init();

// Start session
if (session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
    // session isn't started
    session_start();
}

//var_dump($_SESSION); // Debugging session data

use Pecee\SimpleRouter\SimpleRouter;
// Opcje
SimpleRouter::enableMultiRouteRendering(false);
// Start the routing
echo SimpleRouter::start();