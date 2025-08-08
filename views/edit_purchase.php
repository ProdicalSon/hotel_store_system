<?php
// views/edit_purchase.php
require_once __DIR__ . '/../db/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = intval($_GET['id']);

// Get purchase and product details
$sql = "SELECT purchases.id, products.name AS product_name, purchases.quantity, purchases.purchase_date, purchases.note 
        FROM purchases 
        JOIN products ON purchases.product_id = products.id 
        WHERE purchases.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: ../index.php?error=" . urlencode("Purchase not found."));
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Purchase</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Edit Purchase</h2>
    <?php if (isset($_GET['error'])): ?>
        <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form method="post" action="../controllers/purchase_controller.php">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <label>Product Name:
            <input type="text" name="product_name" value="<?= htmlspecialchars($data['product_name']) ?>" required>
        </label>

        <label>Quantity:
            <input type="number" name="quantity" min="1" value="<?= htmlspecialchars($data['quantity']) ?>" required>
        </label>

        <label>Purchase Date:
            <input type="date" name="purchase_date" value="<?= htmlspecialchars($data['purchase_date']) ?>">
        </label>

        <label>Note:
            <input name="note" value="<?= htmlspecialchars($data['note']) ?>">
        </label>

        <button type="submit" name="action" value="update">Update Purchase</button>
    </form>

    <p><a href="../index.php">Back</a></p>
</div>
</body>
</html>
