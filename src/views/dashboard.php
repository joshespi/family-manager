<h2>Welcome to your dashboard!</h2>

<?php if (!empty($_SESSION['message'])): ?>
    <div class="alert"><?= $_SESSION['message'] ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>


<?php

if (isset($permissions) && in_array('parent_user', $permissions)) {
    include __DIR__ . '/roles/parent.php';
}

if (isset($permissions) && in_array('child_user', $permissions)) {
    include __DIR__ . '/roles/child.php';
}
echo '<hr>';
include __DIR__ . '/tasks/index.php';
?>