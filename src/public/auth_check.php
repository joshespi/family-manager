<?php

use App\Controllers\AuthController;
use App\Models\User;

// Ensure user is logged in
if (!AuthController::check()) {
    header('Location: /');
    exit;
}


$userPermissions = User::getPermissions($_SESSION['user_id']);
$pdo = Database::getConnection();
$user = User::findById($_SESSION['user_id']);
