<?php

namespace App\Controllers;

use App\Models\Timer;

class TimerController
{
    private $timer;

    public function __construct($pdo)
    {
        $this->timer = new Timer($pdo);
    }
    public function start($user_id, $task_id = null)
    {
        return $this->timer->startTimer($user_id, $task_id);
    }

    public function stop($timer_id)
    {
        return $this->timer->stopTimer($timer_id);
    }

    public function getActive($user_id)
    {
        return $this->timer->getActiveTimer($user_id);
    }
}
