<?php

use App\Controllers\TaskController;
use App\Models\User;

$pdo = Database::getConnection();
$family_id = User::getParentId($pdo, $_SESSION['user_id']);
$taskController = new TaskController($pdo);
$tasks = $taskController->getAllTasks($family_id);

if (empty($tasks)): ?>
    <div class="alert alert-info">No tasks available.</div>
<?php else: ?>
    <h2 class="mb-4">Family Tasks</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-nowrap">Name</th>
                    <th>Description</th>
                    <th class="text-nowrap">Status</th>
                    <th class="text-nowrap">Reward</th>
                    <th class="text-nowrap">Due Date</th>
                    <th class="text-nowrap">Assigned To</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td class="text-nowrap"><strong><?= htmlspecialchars($task['name'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($task['description'] ?? '') ?></td>
                        <td>
                            <?php if (empty($task['completed'])): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="complete_task_id" value="<?= $task['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Complete</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-success">Completed</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap"><?= htmlspecialchars($task['reward_units'] ?? '') ?></td>
                        <td class="text-nowrap"><?= htmlspecialchars($task['due_date'] ?? '') ?></td>
                        <td class="text-nowrap"><?= htmlspecialchars(User::getDisplayName($pdo, $task['assigned_to'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</ul>

<?php
if (isset($permissions) && in_array('parent_user', $permissions)) {
    include __DIR__ . '/create.php';
}
