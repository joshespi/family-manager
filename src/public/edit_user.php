<?php

use App\Controllers\AuthController;

require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($id && $username && $role) {
        AuthController::updateUser($id, $username, $role);
        $_SESSION['system_message'] = "User updated successfully.";
    } else {
        $_SESSION['system_message'] = "Invalid input.";
    }
    header('Location: /admin.php');
    exit;
}
