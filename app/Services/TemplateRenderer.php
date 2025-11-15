<?php

namespace App\Services;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use voku\helper\HtmlMin;
use voku\twig\MinifyHtmlExtension;
use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AuthController;
use App\Controllers\NavigationCmsController;
use App\Services\Interfaces\TemplateRendererInterface;

class TemplateRenderer implements TemplateRendererInterface
{
    private static ?Environment $twig = null;

    public function render(string $template, array $data = []): void
    {
        if (self::$twig === null) {
            $this->initializeTwig();
        }

        echo self::$twig->render($template, $data);
    }

    public function redirect(string $url): void
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit();
    }

    private function initializeTwig(): void
    {
        $csrf_token = SimpleRouter::router()->getCsrfVerifier()->getTokenProvider()->getToken();
        $loader = new FilesystemLoader(dirname(__DIR__, 2) . '/app/Views');
        self::$twig = new Environment($loader);

        // Kompresja HTML
        $minifier = new HtmlMin();
        self::$twig->addExtension(new MinifyHtmlExtension($minifier));

        self::$twig->addGlobal('userinfo', $_SESSION['userinfo'] ?? null);
        self::$twig->addGlobal('frontend_url', $_ENV['FRONTEND_URL'] ?? null);
        self::$twig->addGlobal('csrf_token', $csrf_token);
        self::$twig->addGlobal('isLoggedIn', AuthController::isLoggedIn());
        //self::$twig->addGlobal('currentUser', \App\Controllers\AuthController::getUser());

        $menuController = new NavigationCmsController();
        $menuJson = $menuController->getJsonMenu();

        $menu = json_decode($menuJson !== null ? $menuJson : '[]', true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Błąd dekodowania JSON: ' . json_last_error_msg());
        }

        self::$twig->addGlobal('menu', $menu);
    }
}