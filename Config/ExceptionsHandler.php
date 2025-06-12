<?php

namespace Config;

use App\Exceptions\NotLoggedIn;
use Throwable;
use PDOException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationException;

class ExceptionsHandler
{
    public static function handle(Throwable $e): void
    {
        http_response_code(self::determineStatusCode($e));

        if (self::isApiRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            if ($e instanceof PDOException) {
                self::renderView('pdo_exception', $e);
            } elseif ($e instanceof NotFoundException) {
                self::renderView('not_found', $e);
            } elseif ($e instanceof UnauthorizedException) {
                self::renderView('unauthorized', $e);
            } elseif ($e instanceof ValidationException) {
                self::renderView('validation', $e);
            } elseif ($e instanceof NotLoggedIn) {
                self::renderView('not_logged_in', $e);
            } else {
                self::renderView('general', $e);
            }
        }

        self::log($e);
        die();
    }


    protected static function determineStatusCode(Throwable $e): int
    {
        $code = (int) $e->getCode();
        return $code >= 400 && $code <= 599 ? $code : 500;
    }

    protected static function isApiRequest(): bool
    {
        return str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')
            || ($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json';
    }

    protected static function log(Throwable $e): void
    {

        $uri = $_SERVER['REQUEST_URI'] ?? '';

        $ignored = config('log.ignore_404_paths', []);
        foreach ($ignored as $ignore) {
            if (str_starts_with($uri, $ignore)) {
                return;
            }
        }
        $log = sprintf(
            "[%s] %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        file_put_contents(__DIR__ . '/../logs/app.log', $log, FILE_APPEND);
    }

    public static function renderView(string $view, Throwable $e)
    {
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();
        $debug = getenv('APP_DEBUG') === 'true';
        $url = method_exists($e, 'getUrl') ? $e->getUrl() : null;

        require __DIR__ . "/../resources/views/errors/{$view}.php";
    }
}
