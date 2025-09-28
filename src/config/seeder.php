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
    ->execute([$parentId, 'admin']);

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
        10,          // reward_units
        $task_date,  // due_date
        $parentId,   // assigned_to
        $parentId    // family_id
    ]);

// Additional tasks for each family member

// Task for sub parent
$pdo->prepare("INSERT INTO tasks (name, description, completed, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
    ->execute([
        'Sub Parent Task',
        'Task assigned to sub parent.',
        0,
        15,
        date('Y-m-d', strtotime('+2 weeks')),
        $subParentId,
        $parentId
    ]);

// Tasks for child 1
$pdo->prepare("INSERT INTO tasks (name, description, completed, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
    ->execute([
        'Child 1 Task 1',
        'First task for child 1.',
        0,
        5,
        date('Y-m-d', strtotime('+3 days')),
        $childId1,
        $parentId
    ]);
$pdo->prepare("INSERT INTO tasks (name, description, completed, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
    ->execute([
        'Child 1 Task 2',
        'Second task for child 1.',
        0,
        8,
        date('Y-m-d', strtotime('+5 days')),
        $childId1,
        $parentId
    ]);

// Tasks for child 2
$pdo->prepare("INSERT INTO tasks (name, description, completed, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
    ->execute([
        'Child 2 Task 1',
        'First task for child 2.',
        0,
        7,
        date('Y-m-d', strtotime('+4 days')),
        $childId2,
        $parentId
    ]);
$pdo->prepare("INSERT INTO tasks (name, description, completed, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
    ->execute([
        'Child 2 Task 2',
        'Second task for child 2.',
        0,
        12,
        date('Y-m-d', strtotime('+6 days')),
        $childId2,
        $parentId
    ]);

// Unassigned task (optional)
$pdo->prepare("INSERT INTO tasks (name, description, completed, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?, ?)")
    ->execute([
        'Unassigned Task',
        'This task is not assigned to anyone.',
        0,
        3,
        null,
        null,
        $parentId
    ]);

echo "Seed data inserted.\n";
