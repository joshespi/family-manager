<?php

/** @var string $csrfToken */
?>

<h2 class="text-center mb-4">Login</h2>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
<form method="post" action="index.php" class="p-4 border rounded bg-light mx-auto" style="max-width: 400px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" id="username" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
</form>
<p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a>.</p>