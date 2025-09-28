<?php
require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';



// render the dashboard view
render('dashboard', [
    'title' => 'Dashboard',
    'permissions' => $userPermissions['permissions'],
    'role' => $userPermissions['role'],
    'pdo' => $pdo,
    'user' => $user['username']

]);
