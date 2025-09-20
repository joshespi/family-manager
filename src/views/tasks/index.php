<?php

use App\Controllers\TaskController;
use App\Models\User;

$pdo = Database::getConnection();
$family_id = User::getParentId($pdo, $_SESSION['user_id']);
$taskController = new TaskController($pdo);
$tasks = $taskController->getAllTasks($family_id);


if (isset($permissions) && in_array('parent_user', $permissions)) {
    include __DIR__ . '/create.php';
}

if (empty($tasks)): ?>
    <div class="alert alert-info">No tasks available.</div>
<?php else: ?>
    <h2 class="mb-4">Family Tasks</h2>
    <div class="row g-3">

        <?php foreach ($tasks as $task): ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= htmlspecialchars($task['name'] ?? '') ?></h5>
                        <p class="card-text mb-1"><?= htmlspecialchars($task['description'] ?? '') ?></p>
                        <ul class="list-unstyled mb-2">
                            <?php if (!empty($task['reward_units'])): ?>
                                <li><strong>Reward:</strong> <?= htmlspecialchars($task['reward_units']) ?></li>
                            <?php endif; ?>
                            <?php if (!empty($task['due_date'])): ?>
                                <li><strong>Due Date:</strong> <?= htmlspecialchars($task['due_date']) ?></li>
                            <?php endif; ?>
                            <li><strong>Assigned To:</strong> <?= htmlspecialchars(User::getDisplayName($pdo, $task['assigned_to'])) ?></li>
                        </ul>
                        <div class="d-flex align-items-center">
                            <?php if (empty($task['completed'])): ?>
                                <form method="POST" class="me-2">
                                    <input type="hidden" name="complete_task_id" value="<?= $task['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Complete</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-success">Completed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</ul>