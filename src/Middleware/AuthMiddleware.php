<?php

namespace App\Middleware;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        if (!isset($_SESSION['user_id'])) {
            session_flash('error', 'Please login to access this page');
            redirect('/login');
        }

        return $next();
    }
}