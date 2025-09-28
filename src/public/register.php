<?php
require_once __DIR__ . '/start.php';

use App\Controllers\AuthController;
use App\Controllers\SessionManager;
use App\Models\User;

$pdo = Database::getConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!SessionManager::validateCsrfToken($csrfToken)) {
        $error = "Invalid CSRF token.";
    } else {
        // Input validation & sanitization
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        $validation = User::validateCredentials($username, $password);

        if (!$validation['success']) {
            $error = $validation['message'];
        } else {
            $result = AuthController::register($username, $password, $role, $pdo);
            if ($result['success']) {
                $_SESSION['flash'] = "Registration successful! You can now log in.";
                header("Location: /index.php");
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

$csrfToken = SessionManager::generateCsrfToken();
render('register', [
    'csrfToken' => $csrfToken,
    'error' => $error,
    'title' => 'Register'
]);
