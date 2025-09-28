<h2>User: <?= $user ?></h2>

<?php if (isset($_SESSION['system_message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['system_message']) ?></div>
    <?php unset($_SESSION['system_message']); ?>
<?php endif; ?>


<?php
echo '<hr>';
include __DIR__ . '/tasks/index.php';
?>