<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($title ?? 'App') ?> | Family Manager</title>
    <!-- <link rel="stylesheet" href="/assets/css/reset.css?v=<?= htmlspecialchars($app_Version) ?>">
    <link rel="stylesheet" href="/assets/css/variables.css?v=<?= htmlspecialchars($app_Version) ?>">
    <link rel="stylesheet" href="/assets/css/main.css?v=<?= htmlspecialchars($app_Version) ?>"> -->
</head>

<body>
    <header>
        <h1>Family Manager</h1>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>

            </nav>
        <?php endif; ?>
        <hr>
    </header>
    <main>
        <?php if (!empty($message)): ?>
            <div class="alert"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
    <footer>
        <hr>
        <p>&copy; <?= date('Y') ?> Family Manager</p>
    </footer>
    <?php
    $devMode = filter_var($_ENV['DEV_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN);
    if ($devMode) {
        echo '<div class="debug-info">';
        echo '<h3>Debug Info</h3>';
        echo '<pre>';
        echo '<strong>$_SESSION:</strong>' . "\n" . print_r($_SESSION, true) . "\n";
        echo "<strong>Permissions</strong>: \n" . print_r($permissions ?? [], true) . "\n";
        echo '<strong>Role</strong>: ' . print_r($role ?? 'N/A', true) . "\n";
        echo '<strong>$_POST</strong>:' . "\n" . print_r($_POST, true) . "\n";
        echo '</pre>';
        echo '</div>';
    }
    ?>
</body>

</html>