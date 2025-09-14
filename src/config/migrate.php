
<?php
require_once __DIR__ . '/database.php';

$migrationsDir = __DIR__ . '/../migrations/';

if (!is_dir($migrationsDir)) {
    die("Migrations directory does not exist: $migrationsDir\n");
}
$migrationFiles = glob($migrationsDir . '/*.sql');

$pdo = Database::getConnection();

foreach ($migrationFiles as $file) {
    $sql = file_get_contents($file);
    echo "Running migration: " . basename($file) . "\n";
    $pdo->exec($sql);
}
echo "All migrations applied.\n";
