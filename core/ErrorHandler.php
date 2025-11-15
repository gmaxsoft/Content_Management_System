<?php

namespace Core;

use ErrorException;
use Throwable;
use Core\View;

class ErrorHandler
{
    public static function handleError(int $errno, string $errstr, string  $errfile, int $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException(Throwable $exception): void
    {
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        
        http_response_code($code);

        if ($_ENV['DEBUG'] == 'true') {
            $html = "<h1>PHP - Fatal error</h1>";
            $html .= "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            $html .= "<p>Message: '" . $exception->getMessage() . "'</p>";
            $html .= "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            $html .= "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";

            echo $html;
        } else {
            $log = dirname(__DIR__) . '/resources/logs/error_log_' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($message);

            View::renderTemplate("$code.html");
        }
    }
}
