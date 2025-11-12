<?php

namespace App\Controllers;

use App\Models\Task;
use App\Controllers\LoggerController;

class TaskController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }




    // Create
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
            LoggerController::log(
                $userId,
                'CREATE_TASK',
                "Task '{$data['name']}' created by user ID $userId"
            );
        }

        return $result;
    }




    // Read
    public function getAllTasks($family_id)
    {
        return Task::getAll($this->pdo, $family_id);
    }

    public function getTask($id)
    {
        return Task::getById($this->pdo, $id);
    }
    public function getOpenTasksAssignedToUser($family_id, $user_id)
    {
        return Task::getOpenTasksAssignedToUser($this->pdo, $family_id, $user_id);
    }

    public function getCompletedTasksAssignedToUser($family_id, $user_id)
    {
        return Task::getCompletedTasksAssignedToUser($this->pdo, $family_id, $user_id);
    }

    public function getCompletedTasksForFamily($family_id)
    {
        return Task::getCompletedTasksForFamily($this->pdo, $family_id);
    }
    public function getTotalPointsEarned($user_id)
    {
        return Task::getTotalPointsEarned($this->pdo, $user_id);
    }

    public function getTotalPointsSpent($user_id)
    {
        return Task::getTotalPointsSpent($this->pdo, $user_id);
    }




    // Update
    public function completeTask($id)
    {
        $result = Task::markCompleted($this->pdo, $id);

        // Log the completion if successful
        if ($result) {
            // Get current user ID from session or context
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            LoggerController::log(
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
            LoggerController::log(
                $userId,
                'UPDATE_TASK',
                "Task ID {$data['task_id']} updated by user ID $userId"
            );
        }

        return $result;
    }

    public function uncompleteTask($taskId)
    {

        $taskModel = new Task();
        $taskModel->uncomplete($this->pdo, $taskId);
        $_SESSION['system_message'] = "Task marked as incomplete.";
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        LoggerController::log(
            $userId,
            'UNCOMPLETE_TASK',
            "Task ID $taskId marked as incomplete by user ID $userId"
        );
        return true;
    }



    // Delete

}
