<?php
// controllers/purchase_controller.php
require_once __DIR__ . '/../db/config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $date = norm_date($_POST['purchase_date']);
    $note = esc($_POST['note'] ?? '');

    if($product_id <= 0 || $quantity <= 0) {
        header("Location: ../views/add_purchase.php?error=Invalid input");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO purchases (product_id, quantity, purchase_date, note) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $product_id, $quantity, $date, $note);
    $ok = $stmt->execute();
    $stmt->close();

    if($ok) {
        // Update current_stock
        $u = $conn->prepare("UPDATE products SET current_stock = current_stock + ? WHERE id = ?");
        $u->bind_param("ii", $quantity, $product_id);
        $u->execute();
        $u->close();

        header("Location: ../views/add_purchase.php?success=1");
    } else {
        header("Location: ../views/add_purchase.php?error=DB error");
    }
}
