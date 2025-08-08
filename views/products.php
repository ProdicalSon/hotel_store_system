<?php
// views/products.php
require_once __DIR__ . '/../db/config.php';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = esc($_POST['name']);
    $unit = esc($_POST['unit']);
    $opening = intval($_POST['opening_balance']);
    $price = floatval($_POST['unit_price']);

    $stmt = $conn->prepare("INSERT INTO products (name, unit, opening_balance, unit_price, current_stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidi", $name, $unit, $opening, $price, $opening);
    $stmt->execute();
    header("Location: products.php?added=1");
    exit;
}
$products = $conn->query("SELECT * FROM products ORDER BY name");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Products</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<div class="container">
  <h2>Manage Products</h2>
  <form method="post">
    <label>Name: <input name="name" required></label>
    <label>Unit: <input name="unit" placeholder="e.g., kg, L, pcs"></label>
    <label>Opening Balance: <input name="opening_balance" type="number" min="0" value="0"></label>
    <label>Unit Price: <input name="unit_price" type="number" step="0.01" min="0" value="0.00"></label>
    <button type="submit">Add Product</button>
  </form>

  <h3>Products List</h3>
  <table>
    <thead><tr><th>Name</th><th>Unit</th><th>Opening</th><th>Unit Price</th><th>Current Stock</th></tr></thead>
    <tbody>
      <?php while($row = $products->fetch_assoc()): ?>
      <tr>
        <td><?=htmlspecialchars($row['name'])?></td>
        <td><?=htmlspecialchars($row['unit'])?></td>
        <td><?=$row['opening_balance']?></td>
        <td><?=number_format($row['unit_price'],2)?></td>
        <td><?=$row['current_stock']?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <p><a href="../index.php">Back</a></p>
</div>
</body>
</html>
