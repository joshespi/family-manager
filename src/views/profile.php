<?php

/** @var array $user */
/** @var string $role */
/** @var array $permissions */

use App\Controllers\AuthController;

if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="container my-4">
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title mb-3">User Information</h2>
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($role) ?></p>
        </div>
    </div>
    <?php if (in_array('parent_user', $permissions)): ?>
        <!-- Trigger Button -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createSubUserModal">
            Create a New Sub User
        </button>

        <!-- Modal -->
        <div class="modal fade" id="createSubUserModal" tabindex="-1" aria-labelledby="createSubUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="createSubUserModalLabel">Create a New Sub User</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="profile.php" class="p-2">
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
                            <button type="submit" name="create_user" class="btn btn-primary w-100">Create User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <h3 class="mb-3">Family Accounts</h3>
    <?php if (!empty($subAccounts)): ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
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
                            <td><?= htmlspecialchars(AuthController::getUserRole($sub['id'])) ?></td>
                            <td><?= htmlspecialchars($sub['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No family accounts found.</div>
    <?php endif; ?>
</div>