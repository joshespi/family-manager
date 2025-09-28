<?php
require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';

use App\Models\User;
use App\Models\Logger;


$allUsers = User::fetchAllWithPermissionsAndSettings($pdo);

$filterType = $_GET['type'] ?? null;
$logs = Logger::getAll($pdo, $filterType);

// render the Admin view
render('admin', [
    'title' => 'Admin',
    'permissions' => $userPermissions['permissions'],
    'role' => $userPermissions['role'],
    'pdo' => $pdo,
    'user' => $user['username'],
    'allUsers' => $allUsers,
    'logs' => $logs,
    'filterType' => $filterType,

]);
