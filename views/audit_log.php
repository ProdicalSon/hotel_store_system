<?php
include '../db/config.php';
$result = $conn->query("SELECT * FROM audit_log ORDER BY change_date DESC");
?>
<h2>Audit Log</h2>
<table border="1">
    <tr>
        <th>Date</th>
        <th>Action</th>
        <th>Table</th>
        <th>Record ID</th>
        <th>Old Value</th>
        <th>New Value</th>
        <th>Changed By</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['change_date'] ?></td>
        <td><?= $row['action_type'] ?></td>
        <td><?= $row['table_name'] ?></td>
        <td><?= $row['record_id'] ?></td>
        <td><pre><?= $row['old_value'] ?></pre></td>
        <td><pre><?= $row['new_value'] ?></pre></td>
        <td><?= $row['changed_by'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
