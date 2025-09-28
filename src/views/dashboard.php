<h2>User: <?= $user ?></h2>

<?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>


<?php
echo '<hr>';
include __DIR__ . '/tasks/index.php';
?>