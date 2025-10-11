<?php
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;


class AuthControllerTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        $this->pdo = Database::getConnection();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
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
        $user = AuthController::findByUsername($username);

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

        $parent = AuthController::findByUsername($parentUsername);

        // parent creates a child sub-account
        $subUsername = 'child_' . uniqid();
        $subPassword = 'ChildPass123';
        $subRole = 'child';
        $result = AuthController::createSubAccount($parent['id'], $subUsername, $subPassword, $subRole);
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);

        // non-parent (no permission) cannot create sub-account
        $child = AuthController::findByUsername($subUsername);

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
        $user = AuthController::findByUsername($username);

        // Log in as the user to set $_SESSION['user_id']
        AuthController::login($username, $password);

        // Edit user
        AuthController::updateUser($user['id'], 'editeduser', 'parent');
        $edited = AuthController::getUserById($user['id']);
        $this->assertEquals('editeduser', $edited['username']);
        $this->assertEquals('parent', AuthController::getUserRole($user['id']));
    }

    public function testDeleteUser()
    {
        // Create user
        $username = 'deleteme_' . uniqid();
        $password = 'DeletePass123';
        $role = 'user';
        AuthController::register($username, $password, $role);


        // Log in as the user to set $_SESSION['user_id']
        AuthController::login($username, $password);

        $user = AuthController::findByUsername($username);
        $this->assertNotFalse($user);



        // Delete user
        $result = AuthController::deleteUser($user['id']);
        $this->assertTrue($result);
        $deleted = AuthController::getUserById($user['id']);
        $this->assertFalse($deleted);
    }

    public function testEditUserPermissionDenied()
    {
        // Create two users
        $username1 = 'user1_' . uniqid();
        $username2 = 'user2_' . uniqid();
        $password = 'EditPass123';
        $role = 'user';
        AuthController::register($username1, $password, $role);
        AuthController::register($username2, $password, $role);
        $user1 = AuthController::findByUsername($username1);
        $user2 = AuthController::findByUsername($username2);

        // Log in as user1, try to edit user2
        AuthController::login($username1, $password);
        $result = AuthController::updateUser($user2['id'], 'shouldfail', 'parent');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Permission denied.', $result['message']);
    }

    public function testDeleteUserPermissionDenied()
    {
        // Create two users
        $username1 = 'user1_' . uniqid();
        $username2 = 'user2_' . uniqid();
        $password = 'DeletePass123';
        $role = 'user';
        AuthController::register($username1, $password, $role);
        AuthController::register($username2, $password, $role);
        $user1 = AuthController::findByUsername($username1);
        $user2 = AuthController::findByUsername($username2);

        // Log in as user1, try to delete user2
        AuthController::login($username1, $password);
        $result = AuthController::deleteUser($user2['id']);
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Permission denied.', $result['message']);
    }

    public function testUpdateUserToExistingUsername()
    {
        // Create two users
        $username1 = 'user1_' . uniqid();
        $username2 = 'user2_' . uniqid();
        $password = 'EditPass123';
        $role = 'user';
        AuthController::register($username1, $password, $role);
        AuthController::register($username2, $password, $role);
        $user1 = AuthController::findByUsername($username1);

        // Log in as user1, try to change username to username2
        AuthController::login($username1, $password);
        $result = AuthController::updateUser($user1['id'], $username2, 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Username already exists.', $result['message']);
    }

    public function testDeleteNonExistentUser()
    {
        // Log in as a user
        $username = 'deleter_' . uniqid();
        $password = 'DeletePass123';
        $role = 'user';
        AuthController::register($username, $password, $role);
        AuthController::login($username, $password);

        // Try to delete a non-existent user
        $result = AuthController::deleteUser(999999); // unlikely to exist
        $this->assertFalse($result['success']);
    }

    public function testUpdateUserInvalidRole()
    {
        $username = 'roleuser_' . uniqid();
        $password = 'RolePass123';
        $role = 'user';
        AuthController::register($username, $password, $role);
        $user = AuthController::findByUsername($username);

        AuthController::login($username, $password);
        $result = AuthController::updateUser($user['id'], 'roleuser2', 'invalidrole');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid role specified.', $result['message']);
    }

    public function testUpdateUserEmptyUsername()
    {
        $username = 'emptyuser_' . uniqid();
        $password = 'EmptyPass123';
        $role = 'user';
        AuthController::register($username, $password, $role);
        $user = AuthController::findByUsername($username);

        AuthController::login($username, $password);
        $result = AuthController::updateUser($user['id'], '', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Username cannot be empty.', $result['message']);
    }

    public function testUpdateUserOnlyAdminCanAssignAdminRole()
    {
        // Register a normal user
        $username = 'normal_' . uniqid();
        $password = 'NormalPass123';
        $role = 'user';
        AuthController::register($username, $password, $role);
        $user = AuthController::findByUsername($username);

        AuthController::login($username, $password);
        $result = AuthController::updateUser($user['id'], 'normaluser', 'admin');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Only admins can assign the admin role.', $result['message']);
    }

    public function testCannotDeleteOnlyParent()
    {
        // Register a parent
        $username = 'parent_' . uniqid();
        $password = 'ParentPass123';
        $role = 'parent';
        AuthController::register($username, $password, $role);
        $parent = AuthController::findByUsername($username);

        AuthController::login($username, $password);

        // Try to delete self (only parent in family)
        $result = AuthController::deleteUser($parent['id']);
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Cannot delete the only parent in the family.', $result['message']);
    }

    public function testCannotDeleteLastAdmin()
    {
        // Make sure the seed has run and only one admin exists
        // Log in as the seeded admin
        AuthController::login('admin', 'admin');

        // Find the admin's user ID
        $admin = AuthController::findByUsername('admin');
        $adminId = $admin['id'];

        // Try to delete the last admin
        $result = AuthController::deleteUser($adminId);

        // Debug output if needed
        // var_dump($result);

        $this->assertFalse($result['success']);
        $this->assertEquals('Cannot delete the last admin account.', $result['message']);
    }
}
