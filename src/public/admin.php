<?php
require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';

use App\Controllers\AuthController;
use App\Controllers\LoggerController;


$allUsers = AuthController::getUserPermissionsAndSettings();

$filterType = $_GET['type'] ?? null;
$logs = LoggerController::getAll($filterType);

$userPermissions = $userPermissions ?? ['permissions' => [], 'role' => 'guest'];
$pdo = $pdo ?? null;
$user = $user ?? ['username' => 'Guest'];



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
