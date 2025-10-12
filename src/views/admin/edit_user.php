<?php

$allUsers = $allUsers ?? [];

foreach ($allUsers as $user): ?>
    <div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?= $user['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="edit_user.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel<?= $user['id'] ?>">Edit User: <?= htmlspecialchars($user['username']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                        <div class="mb-3">
                            <label for="username<?= $user['id'] ?>" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username<?= $user['id'] ?>" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="role<?= $user['id'] ?>" class="form-label">Role</label>
                            <select class="form-select" id="role<?= $user['id'] ?>" name="role">
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="parent" <?= $user['role'] === 'parent' ? 'selected' : '' ?>>Parent</option>
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="child" <?= $user['role'] === 'child' ? 'selected' : '' ?>>Child</option>
                            </select>
                        </div>
                        <!-- <div class="mb-3">
                            <label for="display_name<?= $user['id'] ?>" class="form-label">Display Name</label>
                            <input type="text" class="form-control" id="display_name<?= $user['id'] ?>" name="display_name" value="<?= htmlspecialchars($user['display_name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password<?= $user['id'] ?>" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="password<?= $user['id'] ?>" name="password" autocomplete="new-password">
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>