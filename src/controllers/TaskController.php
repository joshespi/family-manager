<?php

namespace App\Controllers;

use App\Models\Task;

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

        return Task::create(
            $this->pdo,
            $data['name'],
            $data['description'],
            $reward_units,
            $due_date,
            $data['assigned_to'],
            $data['family_id']
        );
    }
    public function completeTask($id)
    {
        return Task::markCompleted($this->pdo, $id);
    }
    public function updateTask($data)
    {
        $assigned_to = !empty($data['assigned_to']) ? (int)$data['assigned_to'] : null;
        return Task::update(
            $this->pdo,
            $data['task_id'],
            $data['name'],
            $data['description'],
            $data['reward_units'],
            $data['due_date'],
            $assigned_to
        );
    }
    public function getTasksAssignedToUser($family_id, $user_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE family_id = ? AND assigned_to = ?");
        $stmt->execute([$family_id, $user_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
