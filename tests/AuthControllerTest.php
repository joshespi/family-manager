<?php
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;
use App\Models\User;


class AuthControllerTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        global $pdo;
        $_SESSION = [];
        $pdo = Database::getConnection();
        $this->pdo = $pdo;
        // Clear tables to avoid FK constraint issues
        $this->pdo->exec("DELETE FROM tasks");
        $this->pdo->exec("DELETE FROM users");
        $this->pdo->exec("DELETE FROM user_permissions");
        $this->pdo->exec("DELETE FROM user_settings");
        $this->pdo->exec("DELETE FROM change_log");
    }
    public function testCheckWithoutLogin()
    {
        $this->assertFalse(AuthController::check());
    }

    public function testRegistration()
    {
        $username = 'testuser';
        $maliciousUsername = "testuser'; DROP TABLE users; --";
        $password = 'password123';
        $role = 'user';

        // Test registration, should return success message
        $result = AuthController::register($username, $password, $role);
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Registration successful.', $result['message']);

        // test duplicate registration, should return error message
        AuthController::register($username, $password, $role);
        $result = AuthController::register($username, $password, $role);
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Username already exists.', $result['message']);

        // test with empty username
        $result = AuthController::register('', $password, $role);
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Username and password cannot be empty.', $result['message']);

        // test with short password
        $result = AuthController::register('newuser', '123', $role);
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Password must be 8-64 characters and contain at least one letter and one number.', $result['message']);

        // test with invalid role
        $result = AuthController::register('anotheruser', $password, 'invalidrole');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid role specified.', $result['message']);

        // test with SQL injection attempt in username
        $result = AuthController::register($maliciousUsername, $password, $role);
        $this->assertFalse($result['success']);
    }
    public function testPasswordIsHashedOnRegistration()
    {
        $username = 'hashuser_' . uniqid();
        $password = 'SecurePass123';
        $role = 'user';

        AuthController::register($username, $password, $role);
        $user = User::findByUsername($username);

        // Password in DB should not match plain password
        $this->assertNotEquals($password, $user['password']);

        // Password should verify with password_verify
        $this->assertTrue(password_verify($password, $user['password']));
    }
    public function testLoginLogout()
    {
        $username = 'testuser';
        $password = 'password123';
        $role = 'user';

        AuthController::register($username, $password, $role);

        // Test login with incorrect credentials
        $result = AuthController::login($username, 'wrongpassword');
        $this->assertFalse($result);

        // test login with correct credentials
        $result = AuthController::login($username, $password);
        $this->assertTrue(AuthController::check());
        $this->assertTrue($result);

        // test with non existent user
        $result = AuthController::login('nonexistent', $password);
        $this->assertFalse($result);

        // Test logout
        AuthController::logout();
        $this->assertFalse(AuthController::check());
        $this->assertEmpty($_SESSION, 'Session should be empty after logout.');
    }

    public function testCreateSubAccount()
    {
        // create a parent user with 'parent_user' permission
        $parentUsername = 'parent_' . uniqid();
        $parentPassword = 'ParentPass123';
        $parentRole = 'parent';
        $parentResult = AuthController::register($parentUsername, $parentPassword, $parentRole);
        $this->assertTrue($parentResult['success']);

        $parent = User::findByUsername($parentUsername);

        // parent creates a child sub-account
        $subUsername = 'child_' . uniqid();
        $subPassword = 'ChildPass123';
        $subRole = 'child';
        $result = AuthController::createSubAccount($parent['id'], $subUsername, $subPassword, $subRole);
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);

        // non-parent (no permission) cannot create sub-account
        $child = User::findByUsername($subUsername);

        $result = AuthController::createSubAccount($child['id'], 'subuser1', $subPassword, 'child');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Only parents can create sub-accounts.', $result['message']);

        // invalid role for sub-account
        $result = AuthController::createSubAccount($parent['id'], 'subuser2', $subPassword, 'admin');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid role for sub-account.', $result['message']);
    }
    public function testEditUser()
    {
        // Create user
        $username = 'editme_' . uniqid();
        $password = 'EditPass123';
        $role = 'user';
        AuthController::register($username, $password, $role);
        $user = User::findByUsername($username);

        // Edit user
        User::updateUser($user['id'], 'editeduser', 'parent');
        $edited = User::findById($user['id']);
        $this->assertEquals('editeduser', $edited['username']);
        $this->assertEquals('parent', User::getRole(Database::getConnection(), $user['id']));
    }

    public function testDeleteUser()
    {
        // Create user
        $username = 'deleteme_' . uniqid();
        $password = 'DeletePass123';
        $role = 'user';
        AuthController::register($username, $password, $role);
        $user = User::findByUsername($username);

        // Delete user
        $result = User::deleteUser($user['id']);
        $this->assertTrue($result);
        $deleted = User::findById($user['id']);
        $this->assertFalse($deleted);
    }
}
