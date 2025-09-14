<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public static function login($username, $password)
    {
        $user = User::findByUsername($username);
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
        if (User::findByUsername($username)) {
            return ['success' => false, 'message' => 'Username already exists.'];
        }

        $created = User::create($username, $password, $role);

        if ($created) {
            return ['success' => true, 'message' => 'Registration successful.'];
        }

        return ['success' => false, 'message' => 'Registration failed.'];
    }
    public static function createSubAccount($creatorId, $username, $password, $role)
    {
        $creator = User::findById($creatorId);
        $creatorPerms = User::getPermissions($creatorId);
        if (!$creator || !in_array('create_sub', $creatorPerms['permissions'])) {
            return ['success' => false, 'message' => 'Only parents can create sub-accounts.'];
        }
        if (!in_array($role, ['parent', 'child'])) {
            return ['success' => false, 'message' => 'Invalid role for sub-account.'];
        }
        return User::create($username, $password, $role, $creatorId);
    }
}
