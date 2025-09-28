<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($title ?? 'App') ?> | Family Manager</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" href="/assets/favicon.jpg">
    <!-- <link rel=" stylesheet" href="/assets/css/reset.css?v=<?= htmlspecialchars($app_Version) ?>">
    <link rel="stylesheet" href="/assets/css/variables.css?v=<?= htmlspecialchars($app_Version) ?>">
    <link rel="stylesheet" href="/assets/css/main.css?v=<?= htmlspecialchars($app_Version) ?>"> -->
</head>

<body>
    <header class="bg-primary text-white mb-4">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <a href="dashboard.php" class="nav-link text-white">
                    <h1 class="h3 mb-0">Family Manager</h1>
                </a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <nav>
                        <ul class="nav">
                            <?php if (!empty($role) && $role === 'admin'): ?>
                                <li class="nav-item">
                                    <a href="admin.php" class="nav-link text-white">Admin</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a href="profile.php" class="nav-link text-white">Profile</a>
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
    include __DIR__ . '/debug.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>

</html>