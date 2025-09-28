<?php

use App\Controllers\TaskController;
use App\Models\User;

$pdo = Database::getConnection();
$family_id = User::getParentId($pdo, $_SESSION['user_id']);
$taskController = new TaskController($pdo);

// Handle task POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $taskController = new TaskController($pdo);
    $taskController->updateTask([
        'task_id' => (int)$_POST['task_id'],
        'name' => trim($_POST['name']),
        'description' => trim($_POST['description']),
        'reward_units' => isset($_POST['reward_units']) ? (int)$_POST['reward_units'] : null,
        'due_date' => trim($_POST['due_date']),
        'assigned_to' => (int)$_POST['assigned_to'],
    ]);
    $_SESSION['system_message'] = "Task updated!";
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Task retrieval logic
$isParent = isset($permissions) && in_array('parent_user', $permissions);
$isChild = isset($permissions) && in_array('child_user', $permissions);


// Permissions-based task retrieval
if ($isChild) {
    // Children: only their assigned tasks
    $tasks = $taskController->getTasksAssignedToUser($family_id, $_SESSION['user_id']);
} elseif ($isParent) {
    // Parents: all family tasks and their own assigned tasks
    $allFamilyTasks = $taskController->getAllTasks($family_id);
    $myTasks = $taskController->getTasksAssignedToUser($family_id, $_SESSION['user_id']);
    $tasks = $allFamilyTasks; // Default display is all family tasks
} else {
    // Fallback: show nothing
    $tasks = [];
}

// Show create form for parents
if ($isParent) {
    include __DIR__ . '/create.php';
}

// Display logic
?>
<h2 class="mb-4">My Tasks</h2>
<?php if (empty($myTasks)): ?>
    <div class="alert alert-info">No tasks assigned to you.</div>
<?php else: ?>
    <?php $tasks = $myTasks;
    include __DIR__ . '/task_list.php'; ?>
<?php endif; ?>

<?php if ($isParent): ?>
    <h2 class="mt-5 mb-4">Family Tasks</h2>
    <?php if (empty($allFamilyTasks)): ?>
        <div class="alert alert-info">No family tasks available.</div>
    <?php else: ?>
        <?php $tasks = $allFamilyTasks;
        include __DIR__ . '/task_list.php'; ?>
    <?php endif; ?>
<?php endif; ?>