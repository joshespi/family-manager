<?php
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;

class AuthControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $pdo = Database::getConnection();
        // Clear users table before each test (adjust table name if needed)
        $pdo->exec("DELETE FROM users");
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
}
