<?php

namespace App\Models;

use PDO;

class User
{
    public static function findByUsername($username)
    {
        $pdo = \Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function validateCredentials($username, $password)
    {
        if (!preg_match('/^[a-zA-Z0-9_]{5,50}$/', $username)) {
            return ['success' => false, 'message' => 'Username must be 5-50 characters and contain only letters, numbers, and underscores.'];
        }
        if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d).{8,64}$/', $password)) {
            return ['success' => false, 'message' => 'Password must be 8-64 characters and contain at least one letter and one number.'];
        }
        return ['success' => true];
    }
    public static function create($username, $password, $role, $parentId = null)
    {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Username and password cannot be empty.'];
        }
        $validation = self::validateCredentials($username, $password);
        if (!$validation['success']) {
            return $validation;
        }
        $pdo = \Database::getConnection();

        // Check if username already exists
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Username already exists.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, parent_id) VALUES (?, ?, ?)');
        $result = $stmt->execute([$username, $hash, $parentId]);
        if ($result) {
            $userId = $pdo->lastInsertId();
            $stmt2 = $pdo->prepare('INSERT INTO user_permissions (user_id, role) VALUES (?, ?)');
            $result2 = $stmt2->execute([$userId, $role]);
            if ($result2) {
                return ['success' => true, 'message' => 'User created successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to assign role.'];
        }
        return ['success' => false, 'message' => 'Failed to create user.'];
    }
    public static function findById($id)
    {
        $pdo = \Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function getPermissions($userId)
    {
        $pdo = \Database::getConnection();
        $stmt = $pdo->prepare('SELECT role FROM user_permissions WHERE user_id = ?');
        $stmt->execute([$userId]);
        $role = $stmt->fetchColumn();

        $permissions = [];
        if ($role === 'parent' || $role === 'user') {
            $permissions[] = 'parent_user';  // Full access to family management
        }
        if ($role === 'child') {
            $permissions[] = 'child_user';    // Limited access
        }

        return [
            'role' => $role,
            'permissions' => $permissions
        ];
    }
    public static function getSubAccounts($userId)
    {
        $pdo = \Database::getConnection();
        // Get the parent_id of the current user
        $stmt = $pdo->prepare('SELECT parent_id FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $parentId = $stmt->fetchColumn();

        if ($parentId === null) {
            // This is a top-level parent, return all users where parent_id = $userId
            $stmt = $pdo->prepare('SELECT * FROM users WHERE parent_id = ?');
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // This is a child or sub-parent, return all users with the same parent_id (siblings)
            $stmt = $pdo->prepare('SELECT * FROM users WHERE parent_id = ?');
            $stmt->execute([$parentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
