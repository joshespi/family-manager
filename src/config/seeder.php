<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

$pdo = Database::getConnection();

use App\Controllers\AuthController;
use App\Controllers\TaskController;

// Clear tables
$pdo->exec("DELETE FROM tasks");
$pdo->exec("DELETE FROM user_permissions");
$pdo->exec("DELETE FROM user_settings");
$pdo->exec("DELETE FROM users");

// Insert main parent user
$parentPassword = password_hash('admin', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")
    ->execute(['admin', $parentPassword]);
$parentId = $pdo->lastInsertId();

// update parent_id of initial admin user to self
$pdo->prepare("UPDATE users SET parent_id = ? WHERE id = ?")
    ->execute([$parentId, $parentId]);

// Assign role to parent user in user_permissions
$pdo->prepare("INSERT INTO user_permissions (user_id, role) VALUES (?, ?)")
    ->execute([$parentId, 'admin']);

// Optionally, insert default settings for the user in user_settings
$pdo->prepare("INSERT INTO user_settings (user_id, name) VALUES (?, ?)")
    ->execute([$parentId, 'Admin User']);

// Log in as admin to set session for controller actions
AuthController::login('admin', 'admin');

// Create sub parent user
$subParent = AuthController::createSubAccount(
    AuthController::findByUsername('admin')['id'],
    'parentuser1',
    'parentuser1',
    'parent'
);

// Create child users
$child1 = AuthController::createSubAccount(
    AuthController::findByUsername('admin')['id'],
    'childuser1',
    'childuser1',
    'child'
);
$child2 = AuthController::createSubAccount(
    AuthController::findByUsername('admin')['id'],
    'childuser2',
    'childuser2',
    'child'
);

// Add tasks
$parentId = AuthController::findByUsername('admin')['id'];
$subParentId = AuthController::findByUsername('parentuser1')['id'];
$childId1 = AuthController::findByUsername('childuser1')['id'];
$childId2 = AuthController::findByUsername('childuser2')['id'];


$taskController = new TaskController($pdo);

// Add tasks using TaskController
$taskController->createTask([
    'name' => 'Test Task',
    'description' => 'This is a test task for seeder.',
    'reward_units' => 10,
    'due_date' => date('Y-m-d', strtotime('+1 week')),
    'assigned_to' => $parentId,
    'family_id' => $parentId
]);

$taskController->createTask([
    'name' => 'Sub Parent Task',
    'description' => 'Task assigned to sub parent.',
    'reward_units' => 15,
    'due_date' => date('Y-m-d', strtotime('+2 weeks')),
    'assigned_to' => $subParentId,
    'family_id' => $parentId
]);

$taskController->createTask([
    'name' => 'Child 1 Task 1',
    'description' => 'First task for child 1.',
    'reward_units' => 5,
    'due_date' => date('Y-m-d', strtotime('+3 days')),
    'assigned_to' => $childId1,
    'family_id' => $parentId
]);
$taskController->createTask([
    'name' => 'Child 1 Task 2',
    'description' => 'Second task for child 1.',
    'reward_units' => 8,
    'due_date' => date('Y-m-d', strtotime('+5 days')),
    'assigned_to' => $childId1,
    'family_id' => $parentId
]);

$taskController->createTask([
    'name' => 'Child 2 Task 1',
    'description' => 'First task for child 2.',
    'reward_units' => 7,
    'due_date' => date('Y-m-d', strtotime('+4 days')),
    'assigned_to' => $childId2,
    'family_id' => $parentId
]);
$taskController->createTask([
    'name' => 'Child 2 Task 2',
    'description' => 'Second task for child 2.',
    'reward_units' => 12,
    'due_date' => date('Y-m-d', strtotime('+6 days')),
    'assigned_to' => $childId2,
    'family_id' => $parentId
]);

// Optionally, add an unassigned task
$taskController->createTask([
    'name' => 'Unassigned Task',
    'description' => 'This task is not assigned to anyone.',
    'reward_units' => 3,
    'due_date' => null,
    'assigned_to' => null,
    'family_id' => $parentId
]);



echo "Seed data inserted.\n";
