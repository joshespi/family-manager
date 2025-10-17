<?php

namespace App\Controllers;

class SessionManager
{
    public static function validateCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    private static function checkExpiration()
    {
        $timeout = 30 * 60; // minutes
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::destroy();
            header('Location: /index.php?expired=1');
            exit();
        }
        $_SESSION['last_activity'] = time();
    }



    // Create
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => true,      // Only send cookie over HTTPS
                'httponly' => true,    // Prevent JS access
                'samesite' => 'Strict' // Prevent CSRF
            ]);
            session_start();
        }
        // Add session expiration logic
        self::checkExpiration();
    }

    public static function regenerate()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }


    // Read
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }



    // Update
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }



    // Delete
    public static function destroy()
    {
        // Remove session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        $_SESSION = [];
        session_unset();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
