<?php
require_once __DIR__ . '/start.php';

use App\Controllers\AuthController;
use App\Controllers\SessionManager;

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!SessionManager::validateCsrfToken($csrfToken)) {
        $error = "Invalid CSRF token.";
    } else {
        // Input validation & sanitization
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!preg_match('/^[a-zA-Z0-9_]{5,50}$/', $username)) {
            $error = "Invalid username format.";
        } else {
            $success = AuthController::login($username, $password);
            if ($success) {
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        }
    }
}
$csrfToken = SessionManager::generateCsrfToken();
render('login', [
    'title' => 'Login',
    'csrfToken' => $csrfToken,
    'error' => $error
]);
