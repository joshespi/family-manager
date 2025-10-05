<?php

use App\Controllers\TaskController;
use App\Models\User;

$pdo = Database::getConnection();
$family_id = User::getParentId($pdo, $_SESSION['user_id']);
$taskController = new TaskController($pdo);


?>

<h2>User: <?= $user ?></h2>

<?php if (isset($_SESSION['system_message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['system_message']) ?></div>
    <?php unset($_SESSION['system_message']); ?>
<?php endif; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'complete') {
        $taskController->completeTask((int)$_POST['task_id']);
        $_SESSION['system_message'] = "Task marked as complete!";
    }
    if ($_POST['action'] === 'uncomplete') {
        $taskController->uncompleteTask((int)$_POST['task_id']);
        $_SESSION['system_message'] = "Task marked as incomplete!";
    }
}

echo '<hr>';
include __DIR__ . '/tasks/index.php';
?>