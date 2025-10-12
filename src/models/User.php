<?php

namespace App\Models;

use PDO;
use App\Models\Logger;

class User
{
    // Helpers
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
    public static function canManageUser($actingUserId, $targetUserId)
    {
        if (!$actingUserId || !$targetUserId) return false;

        $actingPerms = self::getPermissions($actingUserId);
        $actingRole = $actingPerms['role'];

        // Admin can manage anyone
        if ($actingRole === 'admin') return true;

        // Parent/user can manage self or family members (same parent_id)
        if (in_array($actingRole, ['parent', 'user'])) {
            $actingUser = self::findBy('id', $actingUserId);
            $targetUser = self::findBy('id', $targetUserId);
            if (!$actingUser || !$targetUser) return false;

            // Self or same family
            if (
                $actingUserId == $targetUserId ||
                $actingUser['parent_id'] == $targetUser['parent_id']
            ) {
                return true;
            }
        }

        // Otherwise, not allowed
        return false;
    }



    // Create
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
            return ['success' => false, 'message' => 'This username is already taken. Please choose another.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, parent_id) VALUES (?, ?, ?)');
        $result = $stmt->execute([$username, $hash, $parentId]);
        if ($result) {
            $userId = $pdo->lastInsertId();

            // If this is the initial family user (parentId is null), set parent_id to their own id
            if ($parentId === null) {
                $stmtUpdate = $pdo->prepare('UPDATE users SET parent_id = ? WHERE id = ?');
                $stmtUpdate->execute([$userId, $userId]);
            }

            $stmt2 = $pdo->prepare('INSERT INTO user_permissions (user_id, role) VALUES (?, ?)');
            $result2 = $stmt2->execute([$userId, $role]);

            // Insert default user_settings row
            $stmtSettings = $pdo->prepare('INSERT INTO user_settings (user_id, name) VALUES (?, ?)');
            $stmtSettings->execute([$userId, $username]);

            if ($result2) {
                return ['success' => true, 'message' => 'User created successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to assign role.'];
        }
        return ['success' => false, 'message' => 'Failed to create user.'];
    }





    // Read
    public static function findBy($field, $value)
    {
        $allowed = ['id', 'username'];
        if (!in_array($field, $allowed)) {
            throw new \InvalidArgumentException("Invalid field for user lookup.");
        }
        $pdo = \Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE $field = ?");
        $stmt->execute([$value]);
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
        if ($role === 'admin') {
            $permissions[] = 'admin_user';    // Admin access
            $permissions[] = 'parent_user';   // Admins also get full family management access
        }

        return [
            'role' => $role,
            'permissions' => $permissions
        ];
    }

    public static function readUserPermission($field, $userId)
    {
        $allowed = ['role'];
        if (!in_array($field, $allowed)) {
            throw new \InvalidArgumentException("Invalid field for user permissions lookup.");
        }
        $pdo = \Database::getConnection();
        $stmt = $pdo->prepare("SELECT $field FROM user_permissions WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function readUserSetting($field, $userId)
    {
        $allowed = ['name', 'email'];
        if (!in_array($field, $allowed)) {
            throw new \InvalidArgumentException("Invalid field for user settings lookup.");
        }
        $pdo = \Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM user_settings WHERE user_id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public static function getAllFamily($userId)
    {
        $pdo = \Database::getConnection();
        // Get current user's info
        $user = self::findBy('id', $userId);
        if (!$user) return [];

        $parentId = $user['parent_id'];

        // Get all users with the same parent_id
        $stmt = $pdo->prepare('SELECT * FROM users WHERE parent_id = ?');
        $stmt->execute([$parentId]);
        $family = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Also include the parent user
        $parent = self::findBy('id', $parentId);
        if ($parent) {
            array_unshift($family, $parent);
        }

        // Remove duplicates by user id
        $unique = [];
        foreach ($family as $member) {
            $unique[$member['id']] = $member;
        }
        return array_values($unique);
    }

    public static function getDisplayName($userId)
    {
        $pdo = \Database::getConnection();
        // Try user_settings first, fallback to users table
        $stmt = $pdo->prepare("SELECT name FROM user_settings WHERE user_id = ?");
        $stmt->execute([$userId]);
        $name = $stmt->fetchColumn();
        if ($name) {
            return $name;
        }
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 'Unknown';
    }

    public static function fetchAllWithPermissionsAndSettings()
    {
        $pdo = \Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT 
                u.id, 
                u.username, 
                u.parent_id, 
                up.role, 
                us.name AS display_name
            FROM users u
            LEFT JOIN user_permissions up ON u.id = up.user_id
            LEFT JOIN user_settings us ON u.id = us.user_id
            ORDER BY u.id ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }




    // Update
    public static function updateUser($id, $username, $role)
    {
        //// Check permissions
        // Check if current user can manage the target user
        $actingUserId = $_SESSION['user_id'] ?? null;
        if (!self::canManageUser($actingUserId, $id)) {
            return ['success' => false, 'message' => 'Permission denied.'];
        }

        // Prevent non-admins from assigning the admin role
        $actingPerms = self::getPermissions($actingUserId);
        if ($role === 'admin' && $actingPerms['role'] !== 'admin') {
            return ['success' => false, 'message' => 'Only admins can assign the admin role.'];
        }
        $pdo = \Database::getConnection();

        // Update username
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$username, $id]);

        // Update role
        $stmt = $pdo->prepare("UPDATE user_permissions SET role = ? WHERE user_id = ?");
        $stmt->execute([$role, $id]);

        // Log the update
        $actingUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        Logger::log(
            $pdo,
            $actingUserId,
            'UPDATE_USER',
            "User ID $id updated: username set to '$username', role set to '$role'"
        );

        return true;
    }



    // Delete
    public static function deleteUser($id)
    {
        $actingUserId = $_SESSION['user_id'] ?? null;
        if (!self::canManageUser($actingUserId, $id)) {
            return ['success' => false, 'message' => 'Permission denied.'];
        }

        $role = self::getPermissions($id)['role'] ?? null;

        // Prevent deleting the last admin
        if ($role === 'admin') {
            $admins = array_filter(self::fetchAllWithPermissionsAndSettings(), fn($u) => $u['role'] === 'admin');
            if (count($admins) <= 1) {
                return ['success' => false, 'message' => 'Cannot delete the last admin account.'];
            }
        }

        // Prevent deleting the only parent in a family
        if ($role === 'parent') {
            $family = self::getAllFamily($id);
            $parentCount = 0;
            foreach ($family as $member) {
                if (self::getPermissions($member['id'])['role'] === 'parent') {
                    $parentCount++;
                }
            }
            if ($parentCount <= 1) {
                return ['success' => false, 'message' => 'Cannot delete the only parent in the family.'];
            }
        }

        $pdo = \Database::getConnection();

        // Log the deletion
        $actingUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        Logger::log(
            $pdo,
            $actingUserId,
            'DELETE_USER',
            "User ID $id deleted"
        );

        // Delete tasks assigned to this user
        $pdo->prepare("DELETE FROM tasks WHERE assigned_to = ?")->execute([$id]);

        // Delete related user_permissions and user_settings
        $pdo->prepare("DELETE FROM user_permissions WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM user_settings WHERE user_id = ?")->execute([$id]);

        // Then delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            return ['success' => true, 'message' => 'User deleted successfully.'];
        }
        return ['success' => false, 'message' => 'Delete failed.'];
    }
}
