<?php
// controllers/requisition_controller.php
require_once __DIR__ . '/../db/config.php';

function log_action($conn, $action, $details) {
    $stmt = $conn->prepare("INSERT INTO audit_log (action, details, log_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $action, $details);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $quantity     = (int)$_POST['quantity'];
    $dept         = trim($_POST['department']);
    $req_date     = $_POST['requisition_date'];
    $note         = trim($_POST['note']);
    $edit_id      = isset($_POST['id']) ? (int)$_POST['id'] : null;

    if (empty($product_name) || $quantity <= 0 || empty($dept)) {
        header("Location: ../views/make_requisition.php?error=Invalid input");
        exit;
    }

    // Ensure product exists or create it
    $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $stmt->bind_result($product_id);
    if (!$stmt->fetch()) {
        $stmt->close();
        $unit = 'pcs'; // default unit, adjust if needed
        $stmt = $conn->prepare("INSERT INTO products (name, unit) VALUES (?, ?)");
        $stmt->bind_param("ss", $product_name, $unit);
        $stmt->execute();
        $product_id = $stmt->insert_id;
    }
    $stmt->close();

    if ($edit_id) {
        // Update requisition
        $stmt = $conn->prepare("UPDATE requisitions SET product_id=?, quantity=?, department=?, requisition_date=?, note=? WHERE id=?");
        $stmt->bind_param("iisssi", $product_id, $quantity, $dept, $req_date, $note, $edit_id);
        if ($stmt->execute()) {
            log_action($conn, "Edit Requisition", "Requisition ID {$edit_id} updated for product {$product_name}");
            header("Location: ../views/make_requisition.php?success=Requisition updated");
        } else {
            header("Location: ../views/make_requisition.php?error=Failed to update requisition");
        }
        $stmt->close();
    } else {
        // Add new requisition
        $stmt = $conn->prepare("INSERT INTO requisitions (product_id, quantity, department, requisition_date, note) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $product_id, $quantity, $dept, $req_date, $note);
        if ($stmt->execute()) {
            log_action($conn, "Add Requisition", "New requisition for {$quantity} {$product_name} to {$dept}");
            header("Location: ../views/make_requisition.php?success=Requisition recorded");
        } else {
            header("Location: ../views/make_requisition.php?error=Failed to record requisition");
        }
        $stmt->close();
    }
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Get product name for log
    $stmt = $conn->prepare("SELECT p.name FROM requisitions r JOIN products p ON r.product_id = p.id WHERE r.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($product_name);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM requisitions WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        log_action($conn, "Delete Requisition", "Requisition ID {$id} for {$product_name} deleted");
        header("Location: ../views/make_requisition.php?success=Requisition deleted");
    } else {
        header("Location: ../views/make_requisition.php?error=Failed to delete requisition");
    }
    $stmt->close();
    exit;
}
?>
