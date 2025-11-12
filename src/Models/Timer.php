<?php

namespace App\Models;

use PDO;

class Timer
{

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function startTimer($user_id, $task_id = null)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO timers (user_id, task_id, start_time, status) VALUES (?, ?, NOW(), 'running')"
        );
        return $stmt->execute([$user_id, $task_id]);
    }

    public function stopTimer($timer_id)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE timers SET end_time = NOW(), status = 'stopped' WHERE id = ? AND end_time IS NULL"
        );
        return $stmt->execute([$timer_id]);
    }

    public function getActiveTimer($user_id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM timers WHERE user_id = ? AND end_time IS NULL ORDER BY start_time DESC LIMIT 1"
        );
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }
}
