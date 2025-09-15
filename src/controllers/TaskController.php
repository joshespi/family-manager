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

    public function getAllTasks()
    {
        return Task::getAll($this->pdo);
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
            $data['assigned_to']
        );
    }
}
