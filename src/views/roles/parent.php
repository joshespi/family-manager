<?php if (in_array('parent_user', $permissions)): ?>
    <h2>Create a New Sub User</h2>
    <form method="POST" action="dashboard.php">
        <label>Username: <input type="text" name="new_username" required></label><br>
        <label>Password: <input type="password" name="new_password" required></label><br>
        <label>Role:
            <select name="new_role">
                <option value="child">Child</option>
                <option value="parent">Parent</option>
            </select>
        </label><br>
        <button type="submit" name="create_user">Create User</button>
    </form>
<?php endif; ?>

<?php if (!empty($subAccounts)): ?>
    <h2>Your Sub Accounts</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Created At</th>
        </tr>
        <?php foreach ($subAccounts as $sub): ?>
            <tr>
                <td><?= htmlspecialchars($sub['username']) ?></td>
                <td>
                    <?php
                    // Fetch role from user_permissions table
                    $pdo = \Database::getConnection();
                    $stmt = $pdo->prepare('SELECT role FROM user_permissions WHERE user_id = ?');
                    $stmt->execute([$sub['id']]);
                    echo htmlspecialchars($stmt->fetchColumn());
                    ?>
                </td>
                <td><?= htmlspecialchars($sub['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>