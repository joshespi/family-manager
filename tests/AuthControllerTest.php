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

    public function testRegisterNewUser()
    {
        $username = 'testuser_' . uniqid();
        $password = 'password123';
        $role = 'user';

        $result = AuthController::register($username, $password, $role);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Registration successful.', $result['message']);
    }

    public function testRegisterExistingUser()
    {
        $username = 'duplicateuser';
        $password = 'password123';
        $role = 'user';

        AuthController::register($username, $password, $role);
        $result = AuthController::register($username, $password, $role);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Username already exists.', $result['message']);
    }

    public function testLoginWithInvalidCredentials()
    {
        $username = 'invaliduser';
        $password = 'validpassword';
        $role = 'user';

        AuthController::register($username, $password, $role);

        $result = AuthController::login($username, 'wrongpassword');

        $this->assertFalse($result);
    }

    public function testLoginWithNonExistentUser()
    {
        $username = 'doesnotexist';
        $password = 'irrelevant';

        $result = AuthController::login($username, $password);

        $this->assertFalse($result);
    }

    public function testRegisterWithEmptyFields()
    {
        $role = 'user';
        $result = AuthController::register('', '', $role);
        $this->assertFalse($result['success']);
    }

    public function testRegistrationRejectsSqlInjectionUsername()
    {
        $maliciousUsername = "testuser'; DROP TABLE users; --";
        $role = 'user';
        $result = AuthController::register($maliciousUsername, 'password3213', $role);
        $this->assertFalse($result['success']);
    }

    public function testRegisterWithShortPassword()
    {
        $role = 'user';
        $result = AuthController::register('validuser', '123', $role);
        $this->assertFalse($result['success']);
    }

    public function testCheckWithoutLogin()
    {
        $this->assertFalse(AuthController::check());
    }

    public function testRegisterLoginLogoutSessionManagement()
    {
        $username = 'sessionuser';
        $password = 'sessionpassword343';
        $role = 'user';

        ob_start();
        AuthController::register($username, $password, $role);
        AuthController::login($username, $password);

        $this->assertTrue(AuthController::check());

        AuthController::logout();
        $this->assertFalse(AuthController::check());
        ob_end_clean();
    }

    public function testLoginWithValidCredentials()
    {
        $username = 'validuser';
        $password = 'validpassword342';
        $role = 'user';

        AuthController::register($username, $password, $role);

        $result = AuthController::login($username, $password);

        $this->assertTrue($result);
    }
}
