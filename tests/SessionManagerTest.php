<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\SessionManager;

class SessionManagerTest extends TestCase
{
    public function testSessionSanity()
    {
        // Set some session data
        $_SESSION['test'] = 'value';

        // Check if session data is set correctly
        $this->assertEquals('value', $_SESSION['test']);

        // Destroy the session
        SessionManager::destroy();

        // Check if session is destroyed
        $this->assertEmpty($_SESSION);
    }
    public function testCsrfTokenGeneration()
    {
        $token = SessionManager::generateCsrfToken();

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // Default length for CSRF token
    }
    public function testCsrfTokenValidation()
    {
        $token = SessionManager::generateCsrfToken();
        $_SESSION['csrf_token'] = $token;

        $this->assertTrue(SessionManager::validateCsrfToken($token));
        $this->assertFalse(SessionManager::validateCsrfToken('invalid_token'));
    }
    public function testSessionRegenerate()
    {
        SessionManager::start();
        $oldId = session_id();
        SessionManager::regenerate();
        $newId = session_id();

        $this->assertNotSame($oldId, $newId);
    }
    public function testSessionStartTwice()
    {
        SessionManager::start();
        $firstId = session_id();
        SessionManager::start();
        $secondId = session_id();
        $this->assertSame($firstId, $secondId, "Session ID should remain the same if session is already started.");
    }
    public function testDestroyWithoutActiveSession()
    {
        SessionManager::destroy(); // Should not throw
        $this->assertEmpty($_SESSION, "Session should remain empty after destroy with no active session.");
    }
    public function testCsrfTokenIsUnique()
    {
        unset($_SESSION['csrf_token']);
        $token1 = SessionManager::generateCsrfToken();
        unset($_SESSION['csrf_token']);
        $token2 = SessionManager::generateCsrfToken();
        $this->assertNotEquals($token1, $token2, "Each CSRF token should be unique.");
    }
    public function testSessionDataPersistence()
    {
        SessionManager::start();
        $_SESSION['foo'] = 'bar';
        session_write_close();
        SessionManager::start();
        $this->assertEquals('bar', $_SESSION['foo'], "Session data should persist across requests.");
    }
    public function testRegenerateWithoutSession()
    {
        SessionManager::destroy();
        $this->expectNotToPerformAssertions();
        SessionManager::regenerate(); // Should not throw
    }
}
