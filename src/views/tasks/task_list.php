<?php

use App\Controllers\AuthController;
?>

<div class="row g-3">
    <?php foreach ($tasks as $task): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-2"><?= htmlspecialchars($task['name'] ?? '') ?></h5>
                    <p class="card-text mb-1"><?= htmlspecialchars($task['description'] ?? '') ?></p>
                    <ul class="list-unstyled mb-2">
                        <?php if (!empty($task['reward_units'])): ?>
                            <li><strong>Reward:</strong> <?= htmlspecialchars($task['reward_units']) ?></li>
                        <?php endif; ?>
                        <?php if (!empty($task['due_date'])): ?>
                            <li><strong>Due Date:</strong> <?= htmlspecialchars($task['due_date']) ?></li>
                        <?php endif; ?>
                        <?php if (!empty($task['assigned_to'])): ?>
                            <li><strong>Assigned To:</strong> <?= htmlspecialchars(AuthController::getUsernameName($pdo, $task['assigned_to'])) ?></li>
                        <?php endif; ?>
                    </ul>
                    <div class="mt-auto d-flex align-items-center">
                        <?php if (empty($task['completed'])): ?>
                            <form method="POST" class="me-2">
                                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                <input type="hidden" name="action" value="complete">
                                <button type="submit" class="btn btn-success btn-sm">Complete</button>
                            </form>
                            <form method="POST" class="me-2">

                                <!-- Edit Button (shows modal) -->
                                <?php if ($isParent):
                                    include __DIR__ . '/edit.php';
                                ?>
                                    <button type="button"
                                        class="btn btn-primary btn-sm ms-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTaskModal<?= $task['id'] ?>">
                                        Edit
                                    </button>
                                <?php endif; ?>
                            </form>
                        <?php else: ?>
                            <form method="POST" class="mt-auto">
                                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                <input type="hidden" name="action" value="uncomplete">
                                <button type="submit" class="btn btn-warning btn-sm">Uncomplete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>