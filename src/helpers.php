<?php

if (!function_exists('config')) {
    /**
     * Get configuration value by key
     *
     * @param string $key Dot notation key (e.g., 'app.name')
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    function config(string $key, $default = null) {
        $keys = explode('.', $key);
        $filename = array_shift($keys);
        
        static $config = [];
        
        if (!isset($config[$filename])) {
            $path = dirname(__DIR__) . "/config/{$filename}.php";
            $config[$filename] = file_exists($path) ? require $path : [];
        }
        
        $value = $config[$filename];
        
        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        
        return $value;
    }
}

if (!function_exists('old')) {
    /**
     * Get old form input value
     *
     * @param string $key Input field name
     * @param mixed $default Default value if not found
     * @return mixed
     */
    function old(string $key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or retrieve CSRF token
     *
     * @return string
     */
    function csrf_token(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF token field
     *
     * @return string
     */
    function csrf_field(): string {
        return '<input type="hidden" name="token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another URL
     *
     * @param string $path URL path
     * @return void
     */
    function redirect(string $path): void {
        header("Location: {$path}");
        exit;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL for an asset
     *
     * @param string $path Asset path
     * @return string
     */
    function asset(string $path): string {
        return config('app.url') . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('money')) {
    /**
     * Format amount as currency
     *
     * @param float $amount Amount to format
     * @return string
     */
    function money(float $amount): string {
        return number_format($amount, 2);
    }
}

if (!function_exists('session_flash')) {
    /**
     * Flash a message to the session
     *
     * @param string $key Message key
     * @param mixed $value Message value
     * @return void
     */
    function session_flash(string $key, $value): void {
        $_SESSION['flash'][$key] = $value;
    }
}

if (!function_exists('session_has_flash')) {
    /**
     * Check if flash message exists
     *
     * @param string $key Message key
     * @return bool
     */
    function session_has_flash(string $key): bool {
        return isset($_SESSION['flash'][$key]);
    }
}

if (!function_exists('session_get_flash')) {
    /**
     * Get and remove flash message
     *
     * @param string $key Message key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    function session_get_flash(string $key, $default = null) {
        if (!isset($_SESSION['flash'][$key])) {
            return $default;
        }
        
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        
        return $value;
    }
}