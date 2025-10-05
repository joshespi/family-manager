<?php

use App\Controllers\AuthController;

// Ensure user is logged in
if (!AuthController::check()) {
    header('Location: /');
    exit;
}


$userPermissions = AuthController::getUserPermissions($_SESSION['user_id']);
$pdo = Database::getConnection();
$user = AuthController::getUserById($_SESSION['user_id']);
