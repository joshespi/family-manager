<?php
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';

use PHPUnit\Framework\TestCase;
use App\Controllers\LoggerController;
use App\Controllers\AuthController;


class LoggerControllerTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = Database::getConnection();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testLog()
    {
        // Register a test user
        $username = 'testuser123';
        $password = 'password123';
        $role = 'user';

        // Test registration, should return success message
        $result = AuthController::register($username, $password, $role);

        // Arrange
        $userId = $result['user_id'];
        $actionType = 'TEST_ACTION';
        $description = 'This is a test log entry.';

        // Act
        LoggerController::log($userId, $actionType, $description);

        // Assert
        $logs = LoggerController::getAll();
        $logEntry = null;
        foreach ($logs as $log) {
            if ($log['user_id'] == $userId && $log['action_type'] == $actionType && $log['description'] == $description) {
                $logEntry = $log;
                break;
            }
        }

        $this->assertNotFalse($logEntry);
        $this->assertEquals($userId, $logEntry['user_id']);
        $this->assertEquals($actionType, $logEntry['action_type']);
        $this->assertEquals($description, $logEntry['description']);
    }
}
