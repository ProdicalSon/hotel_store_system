<?php
// views/add_purchase.php
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Purchase</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
  <h2>Add Purchase</h2>
  
  <?php if(isset($_GET['success'])): ?>
    <p class="success">Purchase recorded.</p>
  <?php endif; ?>
  
  <?php if(isset($_GET['error'])): ?>
    <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
  <?php endif; ?>

  <form method="post" action="../controllers/purchase_controller.php">
    <label>Product Name:
      <input type="text" name="product_name" placeholder="Enter product name" required>
    </label>
    <label>Quantity:
      <input type="number" name="quantity" min="1" required>
    </label>
    <label>Purchase Date:
      <input type="date" name="purchase_date" value="<?= date('Y-m-d') ?>">
    </label>
    <label>Note:
      <input type="text" name="note" placeholder="Optional">
    </label>
    <button type="submit">Save Purchase</button>
  </form>

  <p><a href="../index.php">Back</a></p>
</div>
</body>
</html>
