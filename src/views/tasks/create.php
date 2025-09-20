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
<form method="POST">

    <label>Task Name: <input type="text" name="name" required></label><br>
    <label>Description: <textarea name="description"></textarea></label><br>
    <label>Reward: <input type="number" name="reward_units"></label><br>
    <label>
        <input type="checkbox" id="has_due_date" onclick="toggleDueDate()"> Has Due Date?
    </label><br>
    <div id="due_date_field" style="display:none;">
        <label>Due Date: <input type="date" name="due_date"></label><br>
    </div>
    <label>Assign To:
        <select name="assigned_to">
            <option value="">-- Select Family Member --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['id']) ?>">
                    <?= htmlspecialchars($user['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <button type="submit">Create Task</button>
</form>
<script>
    function toggleDueDate() {
        var cb = document.getElementById('has_due_date');
        var field = document.getElementById('due_date_field');
        field.style.display = cb.checked ? 'block' : 'none';
    }
</script>