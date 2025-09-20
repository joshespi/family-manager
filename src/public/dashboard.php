<?php
require_once __DIR__ . '/start.php';

use App\Controllers\AuthController;
use App\Models\User;

if (!AuthController::check()) {
    header('Location: /');
    exit;
}

$userPermissions = User::getPermissions($_SESSION['user_id']);
$pdo = Database::getConnection();
$user = User::findById($_SESSION['user_id']);

// render the dashboard view
render('dashboard', [
    'title' => 'Dashboard',
    'permissions' => $userPermissions['permissions'],
    'role' => $userPermissions['role'],
    'pdo' => $pdo,
    'user' => $user['username']

]);
