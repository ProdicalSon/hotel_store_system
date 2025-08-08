<?php
// controllers/requisition_controller.php
require_once __DIR__ . '/../db/config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $department = esc($_POST['department']);
    $date = norm_date($_POST['requisition_date']);
    $note = esc($_POST['note'] ?? '');

    if($product_id <= 0 || $quantity <= 0 || !$department) {
        header("Location: ../views/make_requisition.php?error=Invalid input");
        exit;
    }

    // Check current stock
    $r = $conn->prepare("SELECT current_stock FROM products WHERE id = ?");
    $r->bind_param("i", $product_id);
    $r->execute();
    $r->bind_result($current);
    $r->fetch();
    $r->close();

    if($current === null) {
        header("Location: ../views/make_requisition.php?error=Product not found");
        exit;
    }
    if($current < $quantity) {
        header("Location: ../views/make_requisition.php?error=Insufficient stock");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO requisitions (product_id, department, quantity, requisition_date, note) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $product_id, $department, $quantity, $date, $note);
    $ok = $stmt->execute();
    $stmt->close();

    if($ok) {
        // Update current_stock
        $u = $conn->prepare("UPDATE products SET current_stock = current_stock - ? WHERE id = ?");
        $u->bind_param("ii", $quantity, $product_id);
        $u->execute();
        $u->close();

        header("Location: ../views/make_requisition.php?success=1");
    } else {
        header("Location: ../views/make_requisition.php?error=DB error");
    }
}
