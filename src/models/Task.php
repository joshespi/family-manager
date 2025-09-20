<?php

namespace App\Models;

use PDO;

class Task
{
    public $id;
    public $name;
    public $description;
    public $reward_units;
    public $due_date;
    public $assigned_to;
    public $created_at;
    public $updated_at;

    public static function getAll($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM tasks");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function create($pdo, $name, $description, $reward_units, $due_date, $assigned_to)
    {
        $stmt = $pdo->prepare(
            "INSERT INTO tasks (name, description, reward_units, due_date, assigned_to) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$name, $description, $reward_units, $due_date, $assigned_to]);
    }
    public static function markCompleted($pdo, $id)
    {
        $stmt = $pdo->prepare("UPDATE tasks SET completed = true WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
