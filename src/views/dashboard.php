<?php

/** @var string $user */

use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Controllers\SessionManager;

$pdo = Database::getConnection();
$user_id = $_SESSION['user_id'];
$family_id = AuthController::getParentID($user_id);
$taskController = new TaskController($pdo);

$earned = $taskController->getTotalPointsEarned($user_id);
$spent = $taskController->getTotalPointsSpent($user_id);
$balance = $earned - $spent;

?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center;">
    <div>
        <h2>User: <?= htmlspecialchars($user) ?></h2>
    </div>
    <div>
        <h3 class="alert alert-info">
            Points: <?= htmlspecialchars($balance) ?> (Earned: <?= htmlspecialchars($earned) ?>, Spent: <?= htmlspecialchars($spent) ?>)
        </h3>
    </div>
</div>

<?php if (isset($_SESSION['system_message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['system_message']) ?></div>
    <?php unset($_SESSION['system_message']); ?>
<?php endif; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }
    $task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;
    if ($task_id <= 0) {
        $_SESSION['system_message'] = "Invalid task ID.";
    } elseif ($_POST['action'] === 'complete') {
        $taskController->completeTask($task_id);
        $_SESSION['system_message'] = "Task marked as complete!";
    } elseif ($_POST['action'] === 'uncomplete') {
        $taskController->uncompleteTask($task_id);
        $_SESSION['system_message'] = "Task marked as incomplete!";
    }
}


echo '<hr>';
include __DIR__ . '/timer/index.php';
include __DIR__ . '/tasks/index.php';

?>