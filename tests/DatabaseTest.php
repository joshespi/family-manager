<?php
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../src/config/database.php';

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = Database::getConnection();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
    }

    public function testConnection()
    {
        $this->assertInstanceOf(\PDO::class, $this->pdo);
    }
}
