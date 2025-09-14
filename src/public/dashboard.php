<?php
require_once __DIR__ . '/start.php';

use App\Controllers\AuthController;
use App\Models\User;

if (!AuthController::check()) {
    header('Location: /');
    exit;
}

$userPermissions = User::getPermissions($_SESSION['user_id']);


// Handle sub user creation
$message = '';
if (
    isset($_POST['create_user']) &&
    $userPermissions['role'] === 'user' &&
    !empty($_POST['new_username']) &&
    !empty($_POST['new_password']) &&
    !empty($_POST['new_role'])
) {
    $result = AuthController::createSubAccount(
        $_SESSION['user_id'],
        $_POST['new_username'],
        $_POST['new_password'],
        $_POST['new_role']
    );
    $_SESSION['message'] = $result['message'];
    header('Location: dashboard.php');
    exit;
}

// Fetch sub-accounts if user has permission
$subAccounts = [];
if (in_array('create_sub', $userPermissions['permissions'])) {
    $subAccounts = User::getSubAccounts($_SESSION['user_id']);
}


render('dashboard', [
    'title' => 'Dashboard',
    'message' => $message,
    'permissions' => $userPermissions['permissions'],
    'role' => $userPermissions['role'],
    'subAccounts' => $subAccounts
]);
