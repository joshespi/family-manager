<?php

use App\Controllers\TaskController;
use App\Models\User;

// Fetch users (family members) from the database
$users = User::getAllFamily($pdo, $_SESSION['user_id']);
$parent_id = User::getParentId($pdo, $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_task_id'])) {
    $taskController = new TaskController($pdo);
    $success = $taskController->completeTask((int)$_POST['complete_task_id']);
    $_SESSION['system_message'] = $success ? "Task marked as completed!" : "Error completing task.";
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new TaskController($pdo);
    $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
    $reward_units = isset($_POST['reward_units']) && $_POST['reward_units'] !== '' ? (float)$_POST['reward_units'] : null;
    $success = $controller->createTask([
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'reward_units' => $reward_units,
        'due_date' => $_POST['due_date'],
        'assigned_to' => $assigned_to,
        'family_id' => $parent_id
    ]);
    $_SESSION['system_message'] = $success ? "Task created!" : "Error creating task.";
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

if (isset($_SESSION['system_message'])) {
    echo "<p>{$_SESSION['system_message']}</p>";
    unset($_SESSION['system_message']);
}

?>
<h2>Create Task</h2>
<form method="POST" class="p-3 border rounded bg-light mb-4">
    <div class="mb-3">
        <label for="task_name" class="form-label">Task Name:</label>
        <input type="text" id="task_name" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description:</label>
        <textarea id="description" name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label for="reward_units" class="form-label">Reward:</label>
        <input type="number" id="reward_units" name="reward_units" class="form-control">
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="has_due_date" onclick="toggleDueDate()">
        <label class="form-check-label" for="has_due_date">Has Due Date?</label>
    </div>
    <div id="due_date_field" style="display:none;" class="mb-3">
        <label for="due_date" class="form-label">Due Date:</label>
        <input type="date" id="due_date" name="due_date" class="form-control">
    </div>
    <div class="mb-3">
        <label for="assigned_to" class="form-label">Assign To:</label>
        <select id="assigned_to" name="assigned_to" class="form-select">
            <option value="">-- Select Family Member --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['id']) ?>">
                    <?= htmlspecialchars($user['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Create Task</button>
</form>
<script>
    function toggleDueDate() {
        var cb = document.getElementById('has_due_date');
        var field = document.getElementById('due_date_field');
        field.style.display = cb.checked ? 'block' : 'none';
    }
</script>