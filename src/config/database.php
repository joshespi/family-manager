<?php

class Database
{
    private static $pdo = null;

    public static function getConnection()
    {
        if (self::$pdo === null) {
            $host = $_ENV['DB_HOST'] ?: 'localhost';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $db   = $_ENV['DB_NAME'] ?: 'application_db';
            $user = $_ENV['DB_USER'] ?: 'placebo';
            $pass = $_ENV['DB_PASS'] ?: '';
            $charset = 'utf8mb4';

            // If running outside Docker, use localhost instead of famman_db
            if ($host === 'famman_db' && !file_exists('/.dockerenv')) {
                $host = '127.0.0.1';
            }

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$pdo;
    }
}
// Usage example
// $db = Database::getConnection();
// $stmt = $db->query('SELECT * FROM chores');
// $chores = $stmt->fetchAll();
// print_r($chores);
