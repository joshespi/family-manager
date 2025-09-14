<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../views/helpers.php';

use App\Controllers\SessionManager;

SessionManager::start();
