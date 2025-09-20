<h2 class="text-center mb-4">Register</h2>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?> <a href="/register.php" class="alert-link">Try again</a>.</div>
<?php endif; ?>
<form method="post" action="register.php" class="p-4 border rounded bg-light mx-auto" style="max-width: 400px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" id="username" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Register</button>
</form>
<p class="mt-3 text-center">Already have an account? <a href="index.php">Login here</a>.</p>