<?php
require_once __DIR__ . '/start.php';

use App\Controllers\AuthController;
use App\Controllers\SessionManager;



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
        $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';


        $result = AuthController::register($username, $password, $role);
        if ($result['success']) {
            $_SESSION['flash'] = "Registration successful! You can now log in.";
            header("Location: /index.php");
            exit;
        } else {
            $error = $result['message'];
        }
    }
}


$csrfToken = SessionManager::generateCsrfToken();
render('register', [
    'csrfToken' => $csrfToken,
    'error' => $error,
    'title' => 'Register'
]);
