<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal<?= $task['id'] ?>" tabindex="-1" aria-labelledby="editTaskModalLabel<?= $task['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="editTaskModalLabel<?= $task['id'] ?>">Edit Task</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                    <div class="mb-3">
                        <label for="edit_task_name<?= $task['id'] ?>" class="form-label">Task Name:</label>
                        <input type="text" id="edit_task_name<?= $task['id'] ?>" name="name" class="form-control" value="<?= htmlspecialchars($task['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description<?= $task['id'] ?>" class="form-label">Description:</label>
                        <textarea id="edit_description<?= $task['id'] ?>" name="description" class="form-control"><?= htmlspecialchars($task['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_reward_units<?= $task['id'] ?>" class="form-label">Reward:</label>
                        <input type="number" id="edit_reward_units<?= $task['id'] ?>" name="reward_units" class="form-control" value="<?= htmlspecialchars($task['reward_units']) ?>">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="edit_has_due_date<?= $task['id'] ?>" onclick="toggleEditDueDate(<?= $task['id'] ?>)" <?= !empty($task['due_date']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="edit_has_due_date<?= $task['id'] ?>">Has Due Date?</label>
                    </div>
                    <div id="edit_due_date_field<?= $task['id'] ?>" style="<?= empty($task['due_date']) ? 'display:none;' : '' ?>" class="mb-3">
                        <label for="edit_due_date<?= $task['id'] ?>" class="form-label">Due Date:</label>
                        <input type="date" id="edit_due_date<?= $task['id'] ?>" name="due_date" class="form-control" value="<?= htmlspecialchars($task['due_date']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="edit_assigned_to<?= $task['id'] ?>" class="form-label">Assign To:</label>
                        <select id="edit_assigned_to<?= $task['id'] ?>" name="assigned_to" class="form-select">
                            <option value="">-- Select Family Member --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['id']) ?>" <?= $task['assigned_to'] == $user['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleEditDueDate(id) {
        var cb = document.getElementById('edit_has_due_date' + id);
        var field = document.getElementById('edit_due_date_field' + id);
        var input = document.getElementById('edit_due_date' + id);
        if (cb.checked) {
            field.style.display = 'block';
        } else {
            field.style.display = 'none';
            if (input) input.value = '';
        }
    }
</script>