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

        <?php if (!empty($_SESSION['user_id'])): ?>
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <a class="navbar-brand text-white" href="dashboard.php">Family Manager</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <div class="collapse navbar-collapse" id="mainNavbar">
                            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
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
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>
        </div>
        </div>
    </header>
    <main class="container mb-5">
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <h1><?= $title ?></h1>
        <?= $content ?>
    </main>
    <footer class="bg-light text-center py-3 mt-auto border-top">
        <div class="d-flex justify-content-center flex-wrap gap-2">
            <p class="mb-0">Version <?= htmlspecialchars($app_Version) ?></p>
            <p class="mb-0"> | &copy; <?= date('Y') ?> Family Manager</p>
            <p class="mb-0"> | <a href="https://github.com/joshespi/family-manager/issues">Issue Tracker/Feature Request</a></p>
        </div>
    </footer>
    <?php
    include __DIR__ . '/debug.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>

</html>