<?php

namespace App\Controllers;

use App\Models\User;
use App\Controllers\LoggerController;

class AuthController
{
    public static function login($username, $password)
    {

        $user = AuthController::getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            SessionManager::regenerate();
            SessionManager::set('user_id', $user['id']);
            return true;
        }
        return false;
    }

    public static function logout()
    {
        SessionManager::destroy();
    }

    public static function check()
    {
        return SessionManager::get('user_id') !== null;
    }

    public static function register($username, $password, $role)
    {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Username and password cannot be empty.'];
        }
        $allowedRoles = ['user', 'admin', 'parent', 'child'];
        if (!in_array($role, $allowedRoles)) {
            return [
                'success' => false,
                'message' => 'Invalid role specified.'
            ];
        }
        $validation = User::validateCredentials($username, $password);
        if (!$validation['success']) {
            return $validation;
        }
        if (AuthController::getUserByUsername($username)) {
            return ['success' => false, 'message' => 'Username already exists.'];
        }

        $created = User::create($username, $password, $role);

        if ($created) {
            // Log the registration
            global $pdo;
            LoggerController::log($pdo, null, 'REGISTER', "New user registered: $username ($role)");
            return ['success' => true, 'message' => 'Registration successful.'];
        }

        return ['success' => false, 'message' => 'Registration failed.'];
    }
    public static function createSubAccount($creatorId, $username, $password, $role)
    {
        $creator = User::findById($creatorId);
        $creatorPerms = User::getPermissions($creatorId);
        if (!$creator || !in_array('parent_user', $creatorPerms['permissions'])) {
            return ['success' => false, 'message' => 'Only parents can create sub-accounts.'];
        }
        if (!in_array($role, ['parent', 'child'])) {
            return ['success' => false, 'message' => 'Invalid role for sub-account.'];
        }
        try {
            $result = User::create($username, $password, $role, $creatorId);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }

        // Log the sub-account creation
        global $pdo;
        if ($result) {
            LoggerController::log(
                $pdo,
                $creatorId,
                'CREATE_SUBACCOUNT',
                "Parent user ID $creatorId created sub-account: $username ($role)"
            );
        }

        return $result;
    }
    public static function getParentID($user_id)
    {
        $user = User::findById($user_id);
        return $user ? $user['parent_id'] : null;
    }
    public static function getUserByUsername($username)
    {
        return User::findByUsername($username);
    }
    public static function getUserById($id)
    {
        return User::findById($id);
    }
    public static function deleteUser($user_id)
    {
        $user = User::findById($user_id);
        if (!$user) {
            return false;
        }

        $result = User::deleteUser($user_id);
        if ($result) {
            // Log the deletion
            global $pdo;
            LoggerController::log($pdo, null, 'DELETE_USER', "User deleted: ID $user_id");
        }
        return $result;
    }
    public static function updateUser($user_id, $newUsername, $newRole)
    {
        $user = User::findById($user_id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }
        $allowedRoles = ['user', 'admin', 'parent', 'child'];
        if (!in_array($newRole, $allowedRoles)) {
            return ['success' => false, 'message' => 'Invalid role specified.'];
        }
        if ($newUsername !== $user['username'] && User::findByUsername($newUsername)) {
            return ['success' => false, 'message' => 'Username already exists.'];
        }

        $result = User::updateUser($user_id, $newUsername, $newRole);
        if ($result) {
            // Log the update
            global $pdo;
            LoggerController::log($pdo, null, 'UPDATE_USER', "User updated: ID $user_id to $newUsername ($newRole)");
            return ['success' => true, 'message' => 'User updated successfully.'];
        }
        return ['success' => false, 'message' => 'Update failed.'];
    }
    public static function getUserRole($userId)
    {
        return User::getRole($userId);
    }
    public static function getUserPermissionsAndSettings()
    {
        return User::fetchAllWithPermissionsAndSettings();
    }
    public static function getUserPermissions($userId)
    {
        return User::getPermissions($userId);
    }
    public static function getSubAccounts($parentId)
    {
        return User::getSubAccounts($parentId);
    }
    public static function getUsernameName($userId)
    {
        $user = User::findById($userId);
        return $user ? $user['username'] : 'Unknown';
    }
}
