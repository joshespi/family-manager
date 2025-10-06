<?php
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';


use PHPUnit\Framework\TestCase;
use App\Controllers\TaskController;
use App\Controllers\AuthController;


class TaskControllerTest extends TestCase
{
    protected $pdo;
    protected $controller;
    protected $user;

    protected function setUp(): void
    {
        $this->pdo = Database::getConnection();
        $this->pdo->beginTransaction();
        $this->user = new AuthController();
        $this->controller = new TaskController($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testCreateTaskAndGetTask()
    {
        $this->user->register('testuser1', 'testpass1', 'user');
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute(['testuser1']);
        $userId = $stmt->fetchColumn();

        $data = [
            'name' => 'Controller Task',
            'description' => 'Created via controller',
            'reward_units' => 10.5,
            'due_date' => '2025-09-15',
            'assigned_to' => $userId,
            'family_id' => $userId
        ];

        $taskId = $this->controller->createTask($data);
        $this->assertNotFalse($taskId, 'Task creation failed');

        $task = $this->controller->getTask($taskId);
        $this->assertNotNull($task, 'Task not found in database');
        $this->assertEquals('Controller Task', $task['name']);
        $this->assertEquals('Created via controller', $task['description']);
        $this->assertEquals(10.5, $task['reward_units']);
        $this->assertEquals('2025-09-15', $task['due_date']);
        $this->assertEquals($userId, $task['assigned_to']);
    }

    // public function testGetAllTasks()
    // {
    //     $result = $this->user->register('testuser123', 'testpass1', 'user');
    //     $this->assertTrue($result['success'], $result['message'] ?? 'Registration failed');
    //     $userId = $result['user_id'];
    //     $familyId = $this->user->getFamilyId($userId);
    //     $this->assertNotEmpty($familyId, 'Family ID should not be empty');
    //     $this->controller->createTask([
    //         'name' => 'Task 1',
    //         'description' => 'Desc 1',
    //         'reward_units' => 1,
    //         'due_date' => null,
    //         'assigned_to' => $userId,
    //         'family_id' => $familyId
    //     ]);
    //     $this->controller->createTask([
    //         'name' => 'Task 2',
    //         'description' => 'Desc 2',
    //         'reward_units' => 2,
    //         'due_date' => null,
    //         'assigned_to' => $userId,
    //         'family_id' => $familyId
    //     ]);

    //     $tasks = $this->controller->getAllTasks($familyId);
    //     $this->assertCount(2, $tasks);
    //     $this->assertEquals('Task 1', $tasks[0]['name']);
    //     $this->assertEquals('Task 2', $tasks[1]['name']);
    // }

    // public function testGetTasksAssignedToUser()
    // {
    //     $this->user->register('testuser1', 'testpass1', 'user');
    //     $userId = $this->pdo->query("SELECT id FROM users WHERE username = 'testuser1'")->fetchColumn();

    //     // Insert another user
    //     $this->pdo->exec("INSERT INTO users (username, password) VALUES ('otheruser', 'otherpass1')");
    //     $otherUserId = $this->pdo->query("SELECT id FROM users WHERE username = 'otheruser'")->fetchColumn();

    //     // Insert tasks for testuser in family 1
    //     $this->controller->createTask([
    //         'name' => 'User Task 1',
    //         'description' => 'Desc 1',
    //         'reward_units' => 3,
    //         'due_date' => '2025-10-01',
    //         'assigned_to' => $userId,
    //         'family_id' => 1
    //     ]);
    //     $this->controller->createTask([
    //         'name' => 'User Task 2',
    //         'description' => 'Desc 2',
    //         'reward_units' => 4,
    //         'due_date' => null,
    //         'assigned_to' => $userId,
    //         'family_id' => 1
    //     ]);
    //     // Insert a task for testuser in another family
    //     $this->controller->createTask([
    //         'name' => 'Other Family Task',
    //         'description' => 'Desc 3',
    //         'reward_units' => 5,
    //         'due_date' => null,
    //         'assigned_to' => $userId,
    //         'family_id' => 2
    //     ]);
    //     // Insert a task for another user in family 1
    //     $this->controller->createTask([
    //         'name' => 'Other User Task',
    //         'description' => 'Desc 4',
    //         'reward_units' => 6,
    //         'due_date' => null,
    //         'assigned_to' => $otherUserId,
    //         'family_id' => 1
    //     ]);

    //     $tasks = $this->controller->getOpenTasksAssignedToUser(1, $userId);
    //     $this->assertCount(2, $tasks);
    //     $taskNames = array_column($tasks, 'name');
    //     $this->assertContains('User Task 1', $taskNames);
    //     $this->assertContains('User Task 2', $taskNames);
    //     foreach ($tasks as $task) {
    //         $this->assertEquals($userId, $task['assigned_to']);
    //         $this->assertEquals(1, $task['family_id']);
    //     }
    // }

    // public function testUpdateTask()
    // {
    //     $this->user->register('testuser1', 'testpass1', 'user');
    //     $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
    //     $stmt->execute(['testuser1']);
    //     $userId = $stmt->fetchColumn();
    //     $familyId = $this->pdo->query("SELECT parent_id FROM users WHERE username = 'testuser1'")->fetchColumn();


    //     $this->controller->createTask([
    //         'name' => 'Old Name',
    //         'description' => 'Old Desc',
    //         'reward_units' => 5,
    //         'due_date' => null,
    //         'assigned_to' => $userId,
    //         'family_id' => $familyId
    //     ]);
    //     $task = $this->controller->getAllTasks($familyId)[0];

    //     $this->controller->updateTask([
    //         'task_id' => $task['id'],
    //         'name' => 'New Name',
    //         'description' => 'New Desc',
    //         'reward_units' => 15,
    //         'due_date' => '2025-12-31',
    //         'assigned_to' => $userId,
    //         'family_id' => $familyId
    //     ]);
    //     $updated = $this->controller->getTask($task['id']);
    //     $this->assertEquals('New Name', $updated['name']);
    //     $this->assertEquals(15, $updated['reward_units']);
    // }

    public function testMarkCompletedUncomplete()
    {
        $result = $this->user->register('completeuser1', 'passtest1', 'user');
        $this->assertTrue($result['success'], $result['message'] ?? 'Registration failed');
        $userId = $result['user_id'];
        $this->assertNotEmpty($userId, 'User ID should not be empty');
        $parent_id = $this->user->getParentID($userId); // Assuming family_id is same as
        // $this->assertNotEmpty($parent_id, 'Parent ID should not be empty');
        $taskId = $this->controller->createTask([
            'name' => 'Complete Me',
            'description' => 'Desc',
            'reward_units' => 1,
            'due_date' => null,
            'assigned_to' => $userId,
            'family_id' => $parent_id
        ]);

        $this->controller->completeTask($taskId);

        $completed = $this->controller->getTask($taskId);
        // $this->assertIsArray($completed, 'Completed task not found');
        $this->assertTrue((bool)$completed['completed']);
        // $this->assertSame(1, $completed['completed'], 'Task should be marked as completed');

        $this->controller->uncompleteTask($taskId);

        $uncompleted = $this->controller->getTask($taskId);
        // $this->assertIsArray($uncompleted, 'Uncompleted task not found');
        $this->assertFalse((bool)$uncompleted['completed'], 'Task should be marked as uncompleted');
        // $this->assertSame(0, $uncompleted['completed'], 'Task should be marked as uncompleted');
    }
}
