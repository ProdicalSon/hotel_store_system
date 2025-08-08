<?php
// controllers/purchase_controller.php
require_once __DIR__ . '/../db/config.php';

// Function to create product if it doesn't exist
function getOrCreateProduct($conn, $productName) {
    $productName = trim($productName);
    if ($productName === '') {
        return null;
    }

    // Check if exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $productId = null;
    $stmt->bind_result($productId);
    if ($stmt->fetch()) {
        $stmt->close();
        return $productId;
    }
    $stmt->close();

    // Insert new product
    $stmt = $conn->prepare("INSERT INTO products (name) VALUES (?)");
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();

    return $newId;
}

// Function to log changes
function logAudit($conn, $action, $details) {
    $stmt = $conn->prepare("INSERT INTO audit_log (action, details, log_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $action, $details);
    $stmt->execute();
    $stmt->close();
}

// ADD new purchase
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $productName   = $_POST['product_name'];
    $quantity      = intval($_POST['quantity']);
    $purchaseDate  = $_POST['purchase_date'];
    $note          = $_POST['note'];

    $productId = getOrCreateProduct($conn, $productName);

    if ($productId) {
        $stmt = $conn->prepare("INSERT INTO purchases (product_id, quantity, purchase_date, note) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $productId, $quantity, $purchaseDate, $note);
        if ($stmt->execute()) {
            logAudit($conn, "ADD_PURCHASE", "Added purchase for '$productName' qty: $quantity");
            header("Location: ../views/dashboard.php?success=Purchase added successfully");
        } else {
            header("Location: ../views/add_purchase.php?error=" . urlencode("Failed to add purchase."));
        }
        $stmt->close();
    } else {
        header("Location: ../views/add_purchase.php?error=" . urlencode("Invalid product name."));
    }
    exit;
}

// UPDATE purchase
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id            = intval($_POST['id']);
    $productName   = $_POST['product_name'];
    $quantity      = intval($_POST['quantity']);
    $purchaseDate  = $_POST['purchase_date'];
    $note          = $_POST['note'];

    $productId = getOrCreateProduct($conn, $productName);

    if ($productId) {
        $stmt = $conn->prepare("UPDATE purchases SET product_id = ?, quantity = ?, purchase_date = ?, note = ? WHERE id = ?");
        $stmt->bind_param("iissi", $productId, $quantity, $purchaseDate, $note, $id);
        if ($stmt->execute()) {
            logAudit($conn, "UPDATE_PURCHASE", "Updated purchase ID $id to '$productName' qty: $quantity");
            header("Location: ../views/dashboard.php?success=Purchase updated successfully");
        } else {
            header("Location: ../views/edit_purchase.php?id=$id&error=" . urlencode("Failed to update purchase."));
        }
        $stmt->close();
    } else {
        header("Location: ../views/edit_purchase.php?id=$id&error=" . urlencode("Invalid product name."));
    }
    exit;
}

// DELETE purchase
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get product name for audit
    $stmt = $conn->prepare("SELECT products.name, purchases.quantity FROM purchases JOIN products ON purchases.product_id = products.id WHERE purchases.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($productName, $quantity);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM purchases WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        logAudit($conn, "DELETE_PURCHASE", "Deleted purchase '$productName' qty: $quantity");
        header("Location: ../views/dashboard.php?success=Purchase deleted successfully");
    } else {
        header("Location: ../views/dashboard.php?error=" . urlencode("Failed to delete purchase."));
    }
    $stmt->close();
    exit;
}
