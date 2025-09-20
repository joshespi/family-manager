<?php

use App\Controllers\TaskController;
use App\Models\User;

$pdo = Database::getConnection();
$taskController = new TaskController($pdo);
$tasks = $taskController->getAllTasks();


if (empty($tasks)): ?>
    <p>No tasks available.</p>
<?php else: ?>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <strong><?= htmlspecialchars($task['name'] ?? '') ?></strong> -
                <?= htmlspecialchars($task['description'] ?? '') ?> |
                Reward: <?= htmlspecialchars($task['reward_units'] ?? '') ?> |
                Due: <?= htmlspecialchars($task['due_date'] ?? '') ?> |
                Assigned to: <?= htmlspecialchars(User::getDisplayName($pdo, $task['assigned_to'])) ?>
                <?php if (empty($task['completed'])): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="complete_task_id" value="<?= $task['id'] ?>">
                        <button type="submit">Complete</button>
                    </form>
                <?php else: ?>
                    <span style="color:green;">Completed</span>
                <?php endif; ?>
            </li>
    <?php endforeach;
    endif;
    ?>

    </ul>

    <?php
    if (isset($permissions) && in_array('parent_user', $permissions)) {
        include __DIR__ . '/create.php';
    }
