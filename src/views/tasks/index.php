<?php

use App\Controllers\TaskController;
use App\Controllers\AuthController;
use App\Controllers\SessionManager;

$pdo = Database::getConnection();
$family_id = AuthController::getParentID($_SESSION['user_id']);
$taskController = new TaskController($pdo);
$completedTasksUser = $taskController->getCompletedTasksAssignedToUser($family_id, $_SESSION['user_id']);
$completedTasks = $taskController->getCompletedTasksForFamily($family_id);

$isParent = isset($permissions) && in_array('parent_user', $permissions);
$isChild = isset($permissions) && in_array('child_user', $permissions);

// Handle task POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }
    $taskController = new TaskController($pdo);
    if ($_POST['action'] === 'edit') {
        // Sanitize and validate input
        $task_id = (int)$_POST['task_id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $reward_units = isset($_POST['reward_units']) ? (float)$_POST['reward_units'] : null;
        $due_date = trim($_POST['due_date']);
        $assigned_to = (int)$_POST['assigned_to'];

        // Validation
        if (strlen($name) < 3 || strlen($name) > 100) {
            $_SESSION['system_message'] = "Task name must be 3-100 characters.";
        } elseif ($reward_units !== null && $reward_units < 0) {
            $_SESSION['system_message'] = "Reward must be a positive number.";
        } elseif ($due_date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $due_date)) {
            $_SESSION['system_message'] = "Invalid due date format.";
        } else {
            $taskController->updateTask([
                'task_id' => $task_id,
                'name' => htmlspecialchars($name),
                'description' => htmlspecialchars($description),
                'reward_units' => $reward_units,
                'due_date' => $due_date,
                'assigned_to' => $assigned_to,
            ]);
            $_SESSION['system_message'] = "Task updated!";
        }
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
    // Filter out tasks assigned to the current user from family tasks
    $allFamilyTasks = array_filter($allFamilyTasks, function ($task) {
        return $task['assigned_to'] != $_SESSION['user_id'];
    });
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
        <div class="alert alert-info">No family tasks available or all tasks assigned to you.</div>
    <?php else: ?>
        <?php $tasks = $allFamilyTasks;
        include __DIR__ . '/task_list.php'; ?>
    <?php endif; ?>
<?php endif; ?>

<?php if ($isParent): ?>
    <?php if (!empty($completedTasks)): ?>
        <div class="alert alert-warning mt-4">
            <strong>Note:</strong> As a parent, you can see and manage all tasks for the family. Children can only see tasks assigned to them.
        </div>
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