<?php
// views/make_requisition.php
require_once __DIR__ . '/../db/config.php';
$products = $conn->query("SELECT id, name, unit, current_stock FROM products ORDER BY name");
$departments = ['Kitchen','Barista/Restaurant','Housekeeping/Accommodation'];
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Make Requisition</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<div class="container">
  <h2>Make Requisition</h2>
  <?php if(isset($_GET['success'])): ?><p class="success">Requisition recorded.</p><?php endif; ?>
  <?php if(isset($_GET['error'])): ?><p class="error"><?=htmlspecialchars($_GET['error'])?></p><?php endif; ?>

  <form method="post" action="../controllers/requisition_controller.php">
    <label>Department:
      <select name="department" required>
        <option value="">--choose--</option>
        <?php foreach($departments as $d): ?>
          <option value="<?=esc($d)?>"><?=htmlspecialchars($d)?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Product:
      <select name="product_id" required>
        <option value="">--choose--</option>
        <?php while($p = $products->fetch_assoc()): ?>
          <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])." ({$p['unit']}) - Stock: {$p['current_stock']}"?></option>
        <?php endwhile; ?>
      </select>
    </label>

    <label>Quantity: <input type="number" name="quantity" min="1" required></label>
    <label>Requisition Date: <input type="date" name="requisition_date" value="<?=date('Y-m-d')?>"></label>
    <label>Note: <input name="note"></label>
    <button type="submit">Issue</button>
  </form>

  <p><a href="../index.php">Back</a></p>
</div>
</body></html>
