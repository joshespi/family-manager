<?php
require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';

use App\Controllers\AuthController;

$subAccounts = [];
// Fetch sub-accounts if user has permission
if (in_array('parent_user', $userPermissions['permissions'])) {
    $subAccounts = AuthController::getSubAccounts($_SESSION['user_id']);
}

// Handle sub user creation
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_user'])) {
        if (
            ($userPermissions['role'] === 'user' || $userPermissions['role'] === 'admin') &&
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
        } else {
            $_SESSION['message'] = 'You do not have permission to create a sub-account or required fields are missing.';
            header('Location: profile.php');
            exit;
        }
    }
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
