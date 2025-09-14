<?php

require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserModelTest extends TestCase
{
    protected function setUp(): void
    {
        $pdo = Database::getConnection();
        $pdo->exec("DELETE FROM users");
    }
    public function testValidateCredentialsInput()
    {
        // Invalid credentials
        $invalid = User::validateCredentials('bad', 'short');
        $this->assertFalse($invalid['success']);

        // Invalid username
        $invalid_user = User::validateCredentials('ab', 'longenough1');
        $this->assertFalse($invalid_user['success']);
        $this->assertStringContainsString('Username must be', $invalid_user['message']);

        // Invalid password
        $invalid_pass = User::validateCredentials('longenoughusername1', 'Pd123');
        $this->assertFalse($invalid_pass['success']);
        $this->assertStringContainsString('Password must be', $invalid_pass['message']);

        // Valid credentials
        $valid = User::validateCredentials('validuser', 'Password123');
        $this->assertTrue($valid['success']);
    }

    public function testCreateAndFindByUsername()
    {
        $result = User::create('testuser', 'Password123', 'parent');
        $this->assertTrue($result['success']);

        $user = User::findByUsername('testuser');
        $this->assertIsArray($user);
        $this->assertEquals('testuser', $user['username']);
    }
    public function testFindByIdReturnsCorrectUser()
    {
        User::create('findme', 'Password123', 'parent');
        $user = User::findByUsername('findme');
        $found = User::findById($user['id']);
        $this->assertIsArray($found);
        $this->assertEquals('findme', $found['username']);
    }
    public function testCreateWithDuplicateUsernameFails()
    {
        User::create('dupeuser', 'Password123', 'parent');
        $result = User::create('dupeuser', 'Password123', 'parent');
        $this->assertFalse($result['success']);
        $this->assertEquals('Username already exists.', $result['message']);
    }

    public function testGetPermissionsReturnsCorrectRoleAndPermissions()
    {
        User::create('permuser', 'Password123', 'parent');
        $user = User::findByUsername('permuser');
        $perms = User::getPermissions($user['id']);
        $this->assertEquals('parent', $perms['role']);
        $this->assertContains('create_sub', $perms['permissions']);
    }
    public function testGetSubAccountsReturnsChildrenAndSiblings()
    {
        // Create parent
        User::create('parentuser', 'Password123', 'parent');
        $parent = User::findByUsername('parentuser');

        // Create children
        User::create('child1', 'Password123', 'child', $parent['id']);
        User::create('child2', 'Password123', 'child', $parent['id']);

        // Test: Get children from parent
        $subs = User::getSubAccounts($parent['id']);
        $usernames = array_column($subs, 'username');
        $this->assertContains('child1', $usernames);
        $this->assertContains('child2', $usernames);

        // Test: Get siblings from one child
        $child1 = User::findByUsername('child1');
        $siblings = User::getSubAccounts($child1['id']);
        $siblingNames = array_column($siblings, 'username');
        $this->assertContains('child1', $siblingNames);
        $this->assertContains('child2', $siblingNames);
    }
}
