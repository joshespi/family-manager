<?php

namespace App\Controllers;

use App\Models\Logger;

class LoggerController
{
    public static function log($pdo, $userId, $actionType, $description)
    {
        Logger::log($pdo, $userId, $actionType, $description);
    }
}
