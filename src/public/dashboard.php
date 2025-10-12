<?php
require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';


$userPermissions = $userPermissions ?? ['permissions' => [], 'role' => 'guest'];
$user = $user ?? ['username' => 'Guest'];
$pdo = $pdo ?? null;



// render the dashboard view
render('dashboard', [
    'title' => 'Dashboard',
    'permissions' => $userPermissions['permissions'],
    'role' => $userPermissions['role'],
    'pdo' => $pdo,
    'user' => $user['username']
]);
