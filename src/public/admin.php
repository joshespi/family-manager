<?php
require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';

use App\Models\User;

$allUsers = User::fetchAllWithPermissionsAndSettings($pdo);

// render the Admin view
render('admin', [
    'title' => 'Admin',
    'permissions' => $userPermissions['permissions'],
    'role' => $userPermissions['role'],
    'pdo' => $pdo,
    'user' => $user['username'],
    'allUsers' => $allUsers

]);
