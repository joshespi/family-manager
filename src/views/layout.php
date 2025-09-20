<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($title ?? 'App') ?> | Family Manager</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel=" stylesheet" href="/assets/css/reset.css?v=<?= htmlspecialchars($app_Version) ?>">
    <link rel="stylesheet" href="/assets/css/variables.css?v=<?= htmlspecialchars($app_Version) ?>">
    <link rel="stylesheet" href="/assets/css/main.css?v=<?= htmlspecialchars($app_Version) ?>"> -->
</head>

<body>
    <header class="bg-primary text-white mb-4">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Family Manager</h1>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <nav>
                        <ul class="nav">
                            <li class="nav-item">
                                <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link text-white">Logout</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container mb-5">
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
    <footer class="bg-light text-center py-3 mt-auto border-top">
        <p class="mb-0">&copy; <?= date('Y') ?> Family Manager</p>
    </footer>
    <?php
    $devMode = filter_var($_ENV['DEV_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN);
    if ($devMode) {
        echo '<div class="container my-4">';
        echo '<div class="card border-danger">';
        echo '<div class="card-header bg-danger text-white">Debug Info</div>';
        echo '<div class="card-body">';
        echo '<pre class="mb-0">';
        echo '<strong>$_SESSION:</strong>' . "\n" . print_r($_SESSION, true) . "\n";
        echo "<strong>Permissions</strong>: \n" . print_r($permissions ?? [], true) . "\n";
        echo '<strong>Role</strong>: ' . print_r($role ?? 'N/A', true) . "\n";
        echo '<strong>$_POST</strong>:' . "\n" . print_r($_POST, true) . "\n";
        echo '</pre>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>

</html>