<?php

namespace App\Controllers;

use App\Models\Logger;
use \PDO;

class LoggerController
{
    // Create
    public static function log($userId, $actionType, $description)
    {
        $pdo = \Database::getConnection();
        Logger::log($pdo, $userId, $actionType, $description);
    }


    // Read
    public static function getAll($filterType = null)
    {
        $pdo = \Database::getConnection();
        return Logger::getAll($pdo, $filterType);
    }

    // Update
    // Delete

}
