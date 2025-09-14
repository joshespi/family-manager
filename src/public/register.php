<?php
require_once __DIR__ . '/start.php';

use App\Controllers\AuthController;
use App\Controllers\SessionManager;
use App\Models\User;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!SessionManager::validateCsrfToken($csrfToken)) {
        echo "Invalid CSRF token.";
        exit;
    }

    // Input validation & sanitization
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    $validation = User::validateCredentials($username, $password);

    $result = AuthController::register($username, $password, $role);
    if ($result['success']) {
        echo "Registration successful. <a href=\"/\">Login here</a>.";
    } else {
        echo $result['message'] . ' <a href="/register.php">Try again</a>.';
    }
    exit;
}
$csrfToken = SessionManager::generateCsrfToken();
render('register', [
    'csrfToken' => $csrfToken,
    'error' => $error,
    'flash' => $success,
    'title' => 'Register'
]);
