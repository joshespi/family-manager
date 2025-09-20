<?php
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';


use PHPUnit\Framework\TestCase;
use App\Controllers\TaskController;
use App\Models\Task;

class TaskControllerTest extends TestCase
{
    protected $pdo;
    protected $controller;

    protected function setUp(): void
    {
        $this->pdo = \Database::getConnection();

        // Clear tables to avoid FK constraint issues
        $this->pdo->exec("DELETE FROM tasks");
        $this->pdo->exec("DELETE FROM users");

        // Insert a user for assignment
        $this->pdo->exec("INSERT INTO users (username, password) VALUES ('testuser', 'pass')");

        $this->controller = new TaskController($this->pdo);
    }

    public function testCreateTaskAndGetTask()
    {
        $userId = $this->pdo->query("SELECT id FROM users WHERE username = 'testuser'")->fetchColumn();
        $data = [
            'name' => 'Controller Task',
            'description' => 'Created via controller',
            'reward_units' => 10.5,
            'due_date' => '2025-09-15',
            'assigned_to' => $userId,
            'family_id' => 1
        ];
        $result = $this->controller->createTask($data);
        $this->assertTrue($result);

        $taskId = $this->pdo->lastInsertId();
        $task = $this->controller->getTask($taskId);
        $this->assertEquals('Controller Task', $task['name']);
        $this->assertEquals('Created via controller', $task['description']);
        $this->assertEquals(10.5, $task['reward_units']);
        $this->assertEquals('2025-09-15', $task['due_date']);
        $this->assertEquals($userId, $task['assigned_to']);
    }

    public function testGetAllTasks()
    {
        $userId = $this->pdo->query("SELECT id FROM users WHERE username = 'testuser'")->fetchColumn();
        $this->controller->createTask([
            'name' => 'Task 1',
            'description' => 'Desc 1',
            'reward_units' => 1,
            'due_date' => null,
            'assigned_to' => $userId,
            'family_id' => 1
        ]);
        $this->controller->createTask([
            'name' => 'Task 2',
            'description' => 'Desc 2',
            'reward_units' => 2,
            'due_date' => null,
            'assigned_to' => $userId,
            'family_id' => 1
        ]);

        $tasks = $this->controller->getAllTasks($family_id = 1);
        $this->assertCount(2, $tasks);
        $this->assertEquals('Task 1', $tasks[0]['name']);
        $this->assertEquals('Task 2', $tasks[1]['name']);
    }
}
