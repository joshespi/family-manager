<?php
require_once __DIR__ . '/start.php';

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

$subAccounts = [];
// Fetch sub-accounts if user has permission
if (in_array('parent_user', $userPermissions['permissions'])) {
    $subAccounts = User::getSubAccounts($_SESSION['user_id']);
}

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
    header('Location: profile.php');
    exit;
}

// Render the profile view
render('profile', [
    'title' => 'Profile',
    'user' => $user,
    'permissions' => $userPermissions['permissions'],
    'role' => $userPermissions['role'],
    'subAccounts' => $subAccounts,
    'pdo' => $pdo,
]);
