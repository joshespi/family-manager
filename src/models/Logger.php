<?php

namespace App\Models;

use PDO;

class Logger
{
    protected $pdo;

    public static function log($pdo, $userId, $actionType, $description)
    {
        $stmt = $pdo->prepare(
            "INSERT INTO change_log (user_id, action_type, description) VALUES (?, ?, ?)"
        );
        $stmt->execute([$userId, $actionType, $description]);
    }

    public static function getAll($pdo, $filterType = null)
    {
        if ($filterType) {
            $stmt = $pdo->prepare("SELECT * FROM change_log WHERE action_type = ? ORDER BY created_at DESC");
            $stmt->execute([$filterType]);
        } else {
            $stmt = $pdo->query("SELECT * FROM change_log ORDER BY created_at DESC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
