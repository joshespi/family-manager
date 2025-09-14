<?php

require_once __DIR__ . '/database.php';

$pdo = Database::getConnection();

// Clear tables
$pdo->exec("DELETE FROM user_permissions");
$pdo->exec("DELETE FROM users");

// Insert main parent user
$parentPassword = password_hash('regularuser1', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")
    ->execute(['regularuser1', $parentPassword]);
$parentId = $pdo->lastInsertId();

// Assign role to parent user
$pdo->prepare("INSERT INTO user_permissions (user_id, role) VALUES (?, ?)")
    ->execute([$parentId, 'user']);

// Insert second parent sub user
$subParentPassword = password_hash('parentuser1', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, password, parent_id) VALUES (?, ?, ?)")
    ->execute(['parentuser1', $subParentPassword, $parentId]);
$subParentId = $pdo->lastInsertId();

// Assign role to sub parent
$pdo->prepare("INSERT INTO user_permissions (user_id, role) VALUES (?, ?)")
    ->execute([$subParentId, 'parent']);

// Insert child accounts tied to first parent
$childPassword1 = password_hash('childuser1', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, password, parent_id) VALUES (?, ?, ?)")
    ->execute(['childuser1', $childPassword1, $parentId]);
$childId1 = $pdo->lastInsertId();
$pdo->prepare("INSERT INTO user_permissions (user_id, role) VALUES (?, ?)")
    ->execute([$childId1, 'child']);

$childPassword2 = password_hash('childuser2', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, password, parent_id) VALUES (?, ?, ?)")
    ->execute(['childuser2', $childPassword2, $parentId]);
$childId2 = $pdo->lastInsertId();
$pdo->prepare("INSERT INTO user_permissions (user_id, role) VALUES (?, ?)")
    ->execute([$childId2, 'child']);

echo "Seed data inserted.\n";
