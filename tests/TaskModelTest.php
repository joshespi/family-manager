<?php
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';


use PHPUnit\Framework\TestCase;
use App\Models\Task;

class TaskModelTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        $this->pdo = \Database::getConnection();

        // Clear tables only (do NOT create them)
        $this->pdo->exec("DELETE FROM tasks");
        $this->pdo->exec("DELETE FROM users");

        // Insert a user for assignment
        $this->pdo->exec("INSERT INTO users (username, password) VALUES ('testuser', 'pass')");
    }

    public function testCreateTask()
    {
        $userId = $this->pdo->query("SELECT id FROM users WHERE username = 'testuser'")->fetchColumn();
        $result = Task::create($this->pdo, 'Test Task', 'Test Description', 5.5, '2025-09-15', $userId);
        $this->assertTrue($result);

        // Get the last inserted task ID
        $taskId = $this->pdo->lastInsertId();
        $task = Task::getById($this->pdo, $taskId);

        $this->assertEquals('Test Task', $task['name']);
        $this->assertEquals('Test Description', $task['description']);
        $this->assertEquals(5.5, $task['reward_units']);
        $this->assertEquals('2025-09-15', $task['due_date']);
        $this->assertEquals($userId, $task['assigned_to']);
    }

    public function testGetAllReturnsTasks()
    {
        $userId = $this->pdo->query("SELECT id FROM users WHERE username = 'testuser'")->fetchColumn();
        Task::create($this->pdo, 'Task 1', 'Desc 1', 1, null, $userId);
        Task::create($this->pdo, 'Task 2', 'Desc 2', 2, null, $userId);

        $tasks = Task::getAll($this->pdo);
        $this->assertCount(2, $tasks);
        $this->assertEquals('Task 1', $tasks[0]['name']);
        $this->assertEquals('Task 2', $tasks[1]['name']);
    }
}
