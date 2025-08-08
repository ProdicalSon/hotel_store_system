<?php
// views/dashboard.php
require_once __DIR__ . '/../db/config.php';
$res = $conn->query("SELECT * FROM products ORDER BY name");
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Dashboard</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<div class="container">
  <h2>Dashboard - Current Stock</h2>

  <table>
    <thead><tr><th>Product</th><th>Unit</th><th>Current Stock</th><th>Unit Price</th><th>Stock Value</th></tr></thead>
    <tbody>
    <?php while($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?=htmlspecialchars($r['name'])?></td>
        <td><?=htmlspecialchars($r['unit'])?></td>
        <td><?=$r['current_stock']?></td>
        <td><?=number_format($r['unit_price'],2)?></td>
        <td><?=number_format($r['current_stock'] * $r['unit_price'],2)?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <p><a href="../index.php">Back</a></p>
</div>
</body></html>
