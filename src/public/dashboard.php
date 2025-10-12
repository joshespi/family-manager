<?php
require_once __DIR__ . '/start.php';
require_once __DIR__ . '/auth_check.php';

// Ensure $userPermissions is defined
$userPermissions = $userPermissions ?? ['permissions' => [], 'role' => 'guest'];

// Ensure $user is defined
$user = $user ?? ['username' => 'Guest'];

// Ensure $pdo is defined
$pdo = $pdo ?? null;

// render the dashboard view
render('dashboard', [
    'title' => 'Dashboard',
    'permissions' => $userPermissions['permissions'],
    'role' => $userPermissions['role'],
    'pdo' => $pdo,
    'user' => $user['username']

]);
