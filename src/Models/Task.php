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


    // Create
    public static function create($pdo, $name, $description, $reward_units, $due_date, $assigned_to, $family_id)
    {
        $stmt = $pdo->prepare(
            "INSERT INTO tasks (name, description, reward_units, due_date, assigned_to, family_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $result = $stmt->execute([$name, $description, $reward_units, $due_date, $assigned_to, $family_id]);
        return $result ? $pdo->lastInsertId() : false;
    }


    // Read
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

    public static function getOpenTasksAssignedToUser($pdo, $family_id, $user_id)
    {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE family_id = ? AND assigned_to = ? AND completed = 0");
        $stmt->execute([$family_id, $user_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getCompletedTasksAssignedToUser($pdo, $family_id, $user_id)
    {
        $stmt = $pdo->prepare(
            "SELECT * FROM tasks WHERE family_id = :family_id AND assigned_to = :user_id AND completed = 1"
        );
        $stmt->execute([
            ':family_id' => $family_id,
            ':user_id' => $user_id
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getCompletedTasksForFamily($pdo, $family_id)
    {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE family_id = ? AND completed = 1");
        $stmt->execute([$family_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getTotalPointsEarned($pdo, $user_id)
    {
        $stmt = $pdo->prepare("SELECT SUM(reward_units) FROM tasks WHERE assigned_to = ? AND completed = 1 AND reward_units > 0");
        $stmt->execute([$user_id]);
        return (int) $stmt->fetchColumn();
    }

    public static function getTotalPointsSpent($pdo, $user_id)
    {
        $stmt = $pdo->prepare("SELECT SUM(ABS(reward_units)) FROM tasks WHERE assigned_to = ? AND completed = 1 AND reward_units < 0");
        $stmt->execute([$user_id]);
        return (int) $stmt->fetchColumn();
    }



    // Update
    public static function update($pdo, $id, $name, $description, $reward_units, $due_date, $assigned_to)
    {
        // Convert empty due_date to null
        $due_date = empty($_POST['due_date']) ? null : $_POST['due_date'];
        $stmt = $pdo->prepare(
            "UPDATE tasks SET name = ?, description = ?, reward_units = ?, due_date = ?, assigned_to = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $description, $reward_units, $due_date, $assigned_to, $id]);
    }

    public static function markCompleted($pdo, $id)
    {
        $stmt = $pdo->prepare("UPDATE tasks SET completed = true WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function uncomplete($pdo, $taskId)
    {
        $stmt = $pdo->prepare("UPDATE tasks SET completed = 0 WHERE id = ?");
        return $stmt->execute([$taskId]);
    }



    // Delete
}
