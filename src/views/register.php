<h2>Register</h2>
<form method="post" action="register.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="index.php">Login here</a>.</p>