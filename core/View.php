<?php

namespace Core;

use App\Services\Interfaces\TemplateRendererInterface;

/**
 * View - Fasada dla renderowania widoków
 * Używa wstrzykiwania zależności dla lepszego SRP
 */
class View
{
    private static ?TemplateRendererInterface $templateRenderer = null;

    public static function setTemplateRenderer(TemplateRendererInterface $renderer): void
    {
        self::$templateRenderer = $renderer;
    }

	/**
	 * Render a view file
	 * @param string $view The view file
	 * @param array $args Associative array of data to display in the view (optional)
	 * @return void
	 */
	public static function render($view, $args = [])
	{
		extract($args, EXTR_SKIP);
		$file = dirname(__DIR__) . "/app/Views/$view";  // relative to Core directory
		if (is_readable($file)) {
			require $file;
		} else {
			throw new \Exception("$file not found in Views folder");
		}
	}

	public static function redirect($url)
	{
        if (self::$templateRenderer) {
            self::$templateRenderer->redirect($url);
        } else {
            // Fallback dla kompatybilności wstecznej
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
            exit();
        }
	}

	public static function renderTemplate($template, $args = [])
	{
        if (self::$templateRenderer) {
            self::$templateRenderer->render($template, $args);
        } else {
            // Fallback dla kompatybilności wstecznej - używamy starej implementacji
            self::legacyRenderTemplate($template, $args);
        }
	}

    /**
     * Legacy method for backward compatibility
     */
	private static function legacyRenderTemplate($template, $args = [])
	{
		static $twig = null;
		if ($twig === null) {
			$csrf_token = \Pecee\SimpleRouter\SimpleRouter::router()->getCsrfVerifier()->getTokenProvider()->getToken();
			$loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/app/Views');
			$twig = new \Twig\Environment($loader);

			// Kompresja HTML
			$minifier = new \voku\helper\HtmlMin();
			$twig->addExtension(new \voku\twig\MinifyHtmlExtension($minifier));
			$twig->addGlobal('userinfo', $_SESSION['userinfo'] ?? null);
			$twig->addGlobal('frontend_url', $_ENV['FRONTEND_URL'] ?? null);
			$twig->addGlobal('csrf_token', $csrf_token);
			$twig->addGlobal('isLoggedIn', \App\Controllers\AuthController::isLoggedIn());
			$twig->addGlobal('messages', \Core\Messages::getMessages());

			$menuController = new \App\Controllers\NavigationCmsController();
			$menuJson = $menuController->getJsonMenu();

			$menu = json_decode($menuJson !== null ? $menuJson : '[]', true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				die('Błąd dekodowania JSON: ' . json_last_error_msg());
			}

			$twig->addGlobal('menu', $menu);

			echo $twig->render($template, $args);
		}
	}
}
