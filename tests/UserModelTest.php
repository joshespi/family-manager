<?php

require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserModelTest extends TestCase
{
    public function testPasswordIsHashedOnRegistration()
    {
        $userModel = new User();
        $plainPassword = 'MySecret123!';
        $userModel->create('testuser', $plainPassword, 'user');

        $storedUser = $userModel->findByUsername('testuser');
        $this->assertNotEquals($plainPassword, $storedUser['password']);
        $this->assertTrue(password_verify($plainPassword, $storedUser['password']));
    }
    public function testDuplicateUsernameRegistration()
    {
        $userModel = new User();
        $userModel->create('uniqueuser', 'password1', 'user');
        $result = $userModel->create('uniqueuser', 'password2', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success'], 'Should not allow duplicate usernames');
    }
    public function testFindByUsernameReturnsNullForNonExistentUser()
    {
        $userModel = new User();
        $result = $userModel->findByUsername('no_such_user');
        $this->assertFalse($result);
    }
    public function testCreateWithEmptyUsernameFails()
    {
        $userModel = new User();
        $result = $userModel->create('', 'password', 'user');
        $this->assertFalse($result['success'], 'Should not allow empty username');
    }
    public function testCreateWithInvalidUsernameFails()
    {
        $userModel = new User();

        $result = $userModel->create('ab', 'Password123', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);

        $result = $userModel->create('user!@#$', 'Password123', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);

        $result = $userModel->create(str_repeat('a', 51), 'Password123', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }
    public function testCreateWithWeakPasswordFails()
    {
        $userModel = new User();

        $result = $userModel->create('validuser', 'short', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);

        $result = $userModel->create('validuser', 'allletters', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);

        $result = $userModel->create('validuser', '12345678', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }
    public function testCreateWithSqlInjectionUsernameFails()
    {
        $userModel = new User();

        $result = $userModel->create("test'; DROP TABLE users; --", 'Password123', 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }
    public function testCreateWithSqlInjectionPasswordFails()
    {
        $userModel = new User();

        $result = $userModel->create('validuser', "password'; DROP TABLE users; --", 'user');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }
}
