<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class Handler
{
    public function handle(Throwable $exception): void
    {
        if ($exception instanceof ValidationException) {
            session_flash('errors', $exception->getErrors());
            session_flash('old', $_POST);
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
            return;
        }

        if ($this->isApiRequest()) {
            $this->handleApiException($exception);
            return;
        }

        $this->handleWebException($exception);
    }

    protected function isApiRequest(): bool
    {
        return isset($_SERVER['HTTP_ACCEPT']) && 
               strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    }

    protected function handleApiException(Throwable $exception): void
    {
        $statusCode = $this->getStatusCode($exception);
        
        header('Content-Type: application/json');
        http_response_code($statusCode);
        
        echo json_encode([
            'error' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => config('app.debug') ? $exception->getTrace() : []
        ]);
    }

    protected function handleWebException(Throwable $exception): void
    {
        $statusCode = $this->getStatusCode($exception);
        http_response_code($statusCode);
        
        if (file_exists(dirname(__DIR__) . "/Views/errors/{$statusCode}.php")) {
            require dirname(__DIR__) . "/Views/errors/{$statusCode}.php";
            return;
        }
        
        require dirname(__DIR__) . "/Views/errors/500.php";
    }

    protected function getStatusCode(Throwable $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return match (get_class($exception)) {
            'PDOException' => 500,
            'InvalidArgumentException' => 400,
            'RuntimeException' => 500,
            default => 500
        };
    }
}