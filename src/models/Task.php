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

    public static function getAll($pdo, $family_id)
    {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE completed=false AND family_id = ?");
        $stmt->execute([$family_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function create($pdo, $name, $description, $reward_units, $due_date, $assigned_to, $family_id)
    {
        $stmt = $pdo->prepare(
            "INSERT INTO tasks (name, description, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$name, $description, $reward_units, $due_date, $assigned_to, $family_id]);
    }
    public static function markCompleted($pdo, $id)
    {
        $stmt = $pdo->prepare("UPDATE tasks SET completed = true WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public static function update($pdo, $id, $name, $description, $reward_units, $due_date, $assigned_to)
    {
        // Convert empty due_date to null
        $due_date = empty($_POST['due_date']) ? null : $_POST['due_date'];
        $stmt = $pdo->prepare(
            "UPDATE tasks SET name = ?, description = ?, reward_units = ?, due_date = ?, assigned_to = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $description, $reward_units, $due_date, $assigned_to, $id]);
    }
}
