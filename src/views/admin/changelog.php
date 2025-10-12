<?php

/** @var array $logs */
?>
<h2>Change Log</h2>

<table>
    <tr>
        <th>Date</th>
        <th>User</th>
        <th>Type</th>
        <th>Description</th>
    </tr>
    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= htmlspecialchars($log['created_at']) ?></td>
            <td><?= htmlspecialchars($log['user_id'] ?? 'System') ?></td>
            <td><?= htmlspecialchars($log['action_type']) ?></td>
            <td><?= htmlspecialchars($log['description']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>