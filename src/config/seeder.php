<?php

require_once __DIR__ . '/database.php';

$pdo = Database::getConnection();

// Clear tables
$pdo->exec("DELETE FROM tasks");
$pdo->exec("DELETE FROM user_permissions");
$pdo->exec("DELETE FROM users");

// Insert main parent user
$parentPassword = password_hash('regularuser1', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")
    ->execute(['regularuser1', $parentPassword]);
$parentId = $pdo->lastInsertId();

// Update parent_id for the original user to its own id (if needed)
$pdo->prepare("UPDATE users SET parent_id = ? WHERE id = ?")
    ->execute([$parentId, $parentId]);

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

// Insert a test task for the parent user
$task_date = date('Y-m-d', strtotime('+1 week'));
$pdo->prepare("INSERT INTO tasks (name, description, completed, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
    ->execute([
        'Test Task',
        'This is a test task for seeder.',
        0,           // completed
        10,              // reward_units
        $task_date,     // due_date
        $parentId,       // assigned_to
        $parentId        // family_id (or use appropriate family id)
    ]);

echo "Seed data inserted.\n";
