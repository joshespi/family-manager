<?php

use App\Controllers\TaskController;
use App\Models\User;

$pdo = Database::getConnection();
$family_id = User::getParentId($pdo, $_SESSION['user_id']);
$taskController = new TaskController($pdo);
$completedTasksUser = $taskController->getCompletedTasksAssignedToUser($family_id, $_SESSION['user_id']);
$completedTasks = $taskController->getCompletedTasksForFamily($family_id);

$isParent = isset($permissions) && in_array('parent_user', $permissions);
$isChild = isset($permissions) && in_array('child_user', $permissions);

// Handle task POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $taskController = new TaskController($pdo);
    if ($_POST['action'] === 'edit') {
        $taskController->updateTask([
            'task_id' => (int)$_POST['task_id'],
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'reward_units' => isset($_POST['reward_units']) ? (int)$_POST['reward_units'] : null,
            'due_date' => trim($_POST['due_date']),
            'assigned_to' => (int)$_POST['assigned_to'],
        ]);
        $_SESSION['system_message'] = "Task updated!";
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Task retrieval logic
// Permissions-based task retrieval
if ($isChild) {
    // Children: only their assigned tasks
    $tasks = $taskController->getOpenTasksAssignedToUser($family_id, $_SESSION['user_id']);
} elseif ($isParent) {
    // Parents: all family tasks and their own assigned tasks
    $allFamilyTasks = $taskController->getAllTasks($family_id);
    $myTasks = $taskController->getOpenTasksAssignedToUser($family_id, $_SESSION['user_id']);
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

<?php if ($isParent): ?>
    <div class="alert alert-warning mt-4">
        <strong>Note:</strong> As a parent, you can see and manage all tasks for the family. Children can only see tasks assigned to them.
    </div>
    <?php if (empty($completedTasks)): ?>
        <div class="alert alert-info mt-4">No Family Completed TAsks.</div>
    <?php else: ?>
        <h2 class="mt-5 mb-4">My Completed Tasks</h2>
        <?php $tasks = $completedTasks;
        include __DIR__ . '/task_list.php'; ?>
    <?php endif; ?>
<?php elseif ($isChild): ?>
    <?php if (empty($completedTasksUser)): ?>
        <div class="alert alert-info mt-4">No Completed Tasks.</div>
    <?php else: ?>
        <h2 class="mt-5 mb-4">My Completed Tasks</h2>
        <?php $tasks = $completedTasksUser;
        include __DIR__ . '/task_list.php'; ?>
    <?php endif; ?>
<?php endif; ?>