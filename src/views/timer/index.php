<?php


use App\Controllers\TimerController;
use App\Controllers\SessionManager;
use App\Controllers\TaskController;
use App\Controllers\AuthController;

$timerController = new TimerController($pdo);
$activeTimer = $timerController->getActive($_SESSION['user_id']);
$family_id = AuthController::getParentID($_SESSION['user_id']);
$taskController = new TaskController($pdo);

// Handle timer actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!SessionManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }
    if (isset($_POST['start_timer'])) {
        $timerController->start($_SESSION['user_id'], null); // No task_id
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    if (isset($_POST['stop_timer'], $_POST['timer_id'])) {
        // Get timer info before stopping
        $timerId = (int)$_POST['timer_id'];
        $stmt = $pdo->prepare("SELECT * FROM timers WHERE id = ?");
        $stmt->execute([$timerId]);
        $timer = $stmt->fetch(PDO::FETCH_ASSOC);

        $timerController->stop($timerId);

        if ($timer && $timer['start_time']) {
            $start = strtotime($timer['start_time']);
            $end = time(); // Now, since we just stopped it
            $seconds = $end - $start;
            $units = round($seconds / 60); // 1 unit per minute

            // Create deduction task for parent
            $result = $taskController->createTask([
                'name' => 'Screen Time Deduction for ' . (AuthController::getUsernameName($_SESSION['user_id']) ?? 'Unknown User'),
                'description' => "Child used $units units of screen time.",
                'reward_units' => -$units,
                'assigned_to' => '',
                'family_id' => $family_id
            ]);
            if (!$result) {
                error_log("Task creation failed: " . print_r([
                    'assigned_to' => $family_id,
                    'family_id' => $family_id,
                    'units' => $units
                ], true));
            }
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}
?>

<h2>Screen Time Timer</h2>

<?php if ($activeTimer):
    $dt = new DateTime($activeTimer['start_time'], new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone('America/Denver'));
    $denverTime = $dt->format('Y-m-d H:i:s');
?>

    <div class="alert alert-success">
        <strong>Timer Running!</strong><br>
        Started: <?= htmlspecialchars($denverTime) ?> (Mountain Time)<br>
        <span id="elapsed"></span>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SessionManager::generateCsrfToken()) ?>">
            <input type="hidden" name="timer_id" value="<?= $activeTimer['id'] ?>">
            <button type="submit" name="stop_timer" class="btn btn-danger mt-2">Stop Timer</button>
        </form>
    </div>
    <script>
        function updateElapsed() {
            const startString = "<?= $activeTimer['start_time'] ?>";
            if (!startString) {
                document.getElementById('elapsed').textContent = "Elapsed: 0h 0m 0s";
                return;
            }
            const parts = startString.split(/[- :]/);
            if (parts.length < 6) {
                document.getElementById('elapsed').textContent = "Elapsed: 0h 0m 0s";
                return;
            }
            // Create UTC date from server time
            const startUTC = new Date(Date.UTC(
                parseInt(parts[0]), // year
                parseInt(parts[1], 10) - 1, // month (0-based)
                parseInt(parts[2], 10), // day
                parseInt(parts[3], 10), // hour
                parseInt(parts[4], 10), // minute
                parseInt(parts[5], 10) // second
            ));
            // Convert to Denver time
            const startDenver = new Date(startUTC.toLocaleString("en-US", {
                timeZone: "America/Denver"
            }));
            const nowDenver = new Date(new Date().toLocaleString("en-US", {
                timeZone: "America/Denver"
            }));
            const diff = Math.floor((nowDenver - startDenver) / 1000);
            const hours = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;
            document.getElementById('elapsed').textContent =
                `Elapsed: ${hours}h ${minutes}m ${seconds}s (Mountain Time)`;
        }
        setInterval(updateElapsed, 1000);
        updateElapsed();
    </script>
<?php else: ?>
    <form class="d-inline" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SessionManager::generateCsrfToken()) ?>">
        <button type="submit" name="start_timer" class="btn btn-success">
            Start Screen Time Timer
        </button>
    </form>
<?php endif; ?>


<?php

$isParent = isset($permissions) && in_array('parent_user', $permissions);

if ($isParent) {
    $familyMembers = AuthController::getAllFamily($_SESSION['user_id']);
    $timerController = new TimerController($pdo);
    foreach ($familyMembers as $member) {
        // Skip self
        if ($member['id'] == $_SESSION['user_id']) continue;
        $activeTimer = $timerController->getActive($member['id']);
        if ($activeTimer) {
            $dt = new DateTime($activeTimer['start_time'], new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('America/Denver'));
            $denverTime = $dt->format('Y-m-d H:i:s');
            echo '<div class="alert alert-warning">';
            echo htmlspecialchars($member['username']) . ' has a timer running!';
            echo ' Started: ' . htmlspecialchars($denverTime) . ' (Mountain Time)<br>';
            echo '</div>';
        }
    }
}
