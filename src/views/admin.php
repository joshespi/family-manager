<?php

if (!empty($role) && $role !== 'admin') {
    header('Location: /dashboard.php');
    exit;
}
?>
<?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
<h1>Admin Panel</h1>

<h2>User Management</h2>
<table class="table">
    <thead>
        <tr>

            <th>Username</th>

            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allUsers as $user): ?>
            <tr>

                <td><?= htmlspecialchars($user['username']) ?></td>

                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= urlencode($user['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_user.php?id=<?= urlencode($user['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>