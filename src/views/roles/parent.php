<?php if (in_array('parent_user', $permissions)): ?>
    <h2>Create a New Sub User</h2>
    <form method="POST" action="dashboard.php" class="mb-4 p-3 border rounded bg-light">
        <div class="mb-3">
            <label for="new_username" class="form-label">Username:</label>
            <input type="text" id="new_username" name="new_username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Password:</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_role" class="form-label">Role:</label>
            <select id="new_role" name="new_role" class="form-select">
                <option value="child">Child</option>
                <option value="parent">Parent</option>
            </select>
        </div>
        <button type="submit" name="create_user" class="btn btn-primary">Create User</button>
    </form>
<?php endif; ?>

<?php if (!empty($subAccounts)): ?>


    <h2>Your Sub Accounts</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subAccounts as $sub): ?>
                <tr>
                    <td><?= htmlspecialchars($sub['username']) ?></td>
                    <td>
                        <?= htmlspecialchars(\App\Models\User::getRole($pdo, $sub['id'])) ?>
                    </td>
                    <td><?= htmlspecialchars($sub['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>