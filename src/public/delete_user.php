<?php

use App\Controllers\AuthController;

require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';


if (empty($userPermissions['role']) || $userPermissions['role'] !== 'admin') {
    header('Location: /dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    if (AuthController::deleteUser($id)) {
        $_SESSION['system_message'] = "User deleted successfully.";
    } else {
        $_SESSION['system_message'] = "Failed to delete user.";
    }
}

header('Location: /admin.php');
exit;
