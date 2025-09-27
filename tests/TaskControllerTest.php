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
    public function testUpdateTask()
    {
        $userId = $this->pdo->query("SELECT id FROM users WHERE username = 'testuser'")->fetchColumn();
        // Create a task to update
        $this->controller->createTask([
            'name' => 'Original Task',
            'description' => 'Original description',
            'reward_units' => 5,
            'due_date' => '2025-09-20',
            'assigned_to' => $userId,
            'family_id' => 1
        ]);
        $taskId = $this->pdo->lastInsertId();

        // Update the task
        $updateData = [
            'task_id' => $taskId,
            'name' => 'Updated Task',
            'description' => 'Updated description',
            'reward_units' => 15,
            'due_date' => '', // Test clearing due date
            'assigned_to' => $userId
        ];
        $result = $this->controller->updateTask($updateData);
        $this->assertTrue($result);

        // Fetch and assert
        $updatedTask = $this->controller->getTask($taskId);
        $this->assertEquals('Updated Task', $updatedTask['name']);
        $this->assertEquals('Updated description', $updatedTask['description']);
        $this->assertEquals(15, $updatedTask['reward_units']);
        $this->assertNull($updatedTask['due_date']); // Should be null after clearing
        $this->assertEquals($userId, $updatedTask['assigned_to']);
    }
}
