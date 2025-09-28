<?php

require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';

use App\Models\User;

if (empty($userPermissions['role']) || $userPermissions['role'] !== 'admin') {
    header('Location: /dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    if (User::deleteUser($id)) {
        $_SESSION['message'] = "User deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete user.";
    }
}

header('Location: /admin.php');
exit;
