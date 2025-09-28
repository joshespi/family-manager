<?php

namespace App\Controllers;

use App\Models\Task;
use App\Models\Logger;

class TaskController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllTasks($family_id)
    {
        return Task::getAll($this->pdo, $family_id);
    }

    public function getTask($id)
    {
        return Task::getById($this->pdo, $id);
    }
    public function createTask($data)
    {
        $due_date = !empty($data['due_date']) ? $data['due_date'] : null;
        $reward_units = isset($data['reward_units']) ? (float)$data['reward_units'] : 0;
        $assigned_to = !empty($data['assigned_to']) ? (int)$data['assigned_to'] : null;

        $result = Task::create(
            $this->pdo,
            $data['name'],
            $data['description'],
            $reward_units,
            $due_date,
            $assigned_to,
            $data['family_id']
        );

        // Log the task creation if successful
        if ($result) {
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            Logger::log(
                $this->pdo,
                $userId,
                'CREATE_TASK',
                "Task '{$data['name']}' created by user ID $userId"
            );
        }

        return $result;
    }
    public function completeTask($id)
    {
        $result = Task::markCompleted($this->pdo, $id);

        // Log the completion if successful
        if ($result) {
            // Get current user ID from session or context
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            Logger::log(
                $this->pdo,
                $userId,
                'COMPLETE_TASK',
                "Task ID $id marked as completed by user ID $userId"
            );
        }

        return $result;
    }
    public function updateTask($data)
    {
        $assigned_to = !empty($data['assigned_to']) ? (int)$data['assigned_to'] : null;
        $result = Task::update(
            $this->pdo,
            $data['task_id'],
            $data['name'],
            $data['description'],
            $data['reward_units'],
            $data['due_date'],
            $assigned_to
        );

        // Log the task update if successful
        if ($result) {
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            Logger::log(
                $this->pdo,
                $userId,
                'UPDATE_TASK',
                "Task ID {$data['task_id']} updated by user ID $userId"
            );
        }

        return $result;
    }
    public function getOpenTasksAssignedToUser($family_id, $user_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE family_id = ? AND assigned_to = ? AND completed = 0");
        $stmt->execute([$family_id, $user_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
