<?php
// controllers/stock_controller.php
require_once __DIR__ . '/../db/config.php';

/**
 * Get list of products
 */
function get_products() {
    global $conn;
    $res = $conn->query("SELECT * FROM products ORDER BY name");
    $out = [];
    while($r = $res->fetch_assoc()) $out[] = $r;
    return $out;
}

/**
 * Opening stock for a product before a date (date format YYYY-MM-DD)
 * Opening = opening_balance + SUM(purchases where date < $date) - SUM(requisitions where date < $date)
 */
function opening_stock($product_id, $date) {
    global $conn;
    $d = norm_date($date);

    $stmt = $conn->prepare("
        SELECT 
            COALESCE(p.opening_balance,0) as opening_balance,
            COALESCE((SELECT SUM(quantity) FROM purchases WHERE product_id = p.id AND purchase_date < ?),0) as total_purchases_before,
            COALESCE((SELECT SUM(quantity) FROM requisitions WHERE product_id = p.id AND requisition_date < ?),0) as total_issued_before
        FROM products p WHERE p.id = ?");
    $stmt->bind_param("ssi", $d, $d, $product_id);
    $stmt->execute();
    $stmt->bind_result($opening_balance, $p_before, $r_before);
    $stmt->fetch();
    $stmt->close();

    return intval($opening_balance) + intval($p_before) - intval($r_before);
}

/**
 * Sum purchases for a product in date range (inclusive)
 */
function purchased_between($product_id, $start, $end) {
    global $conn;
    $s = norm_date($start);
    $e = norm_date($end);
    $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity),0) FROM purchases WHERE product_id = ? AND purchase_date BETWEEN ? AND ?");
    $stmt->bind_param("iss", $product_id, $s, $e);
    $stmt->execute();
    $stmt->bind_result($sum);
    $stmt->fetch();
    $stmt->close();
    return intval($sum);
}

/**
 * Sum issued (requisitions) for a product in date range (inclusive), optional department filter
 */
function issued_between($product_id, $start, $end, $department = null) {
    global $conn;
    $s = norm_date($start);
    $e = norm_date($end);
    if($department) {
        $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity),0) FROM requisitions WHERE product_id = ? AND requisition_date BETWEEN ? AND ? AND department = ?");
        $stmt->bind_param("isss", $product_id, $s, $e, $department);
    } else {
        $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity),0) FROM requisitions WHERE product_id = ? AND requisition_date BETWEEN ? AND ?");
        $stmt->bind_param("iss", $product_id, $s, $e);
    }
    $stmt->execute();
    $stmt->bind_result($sum);
    $stmt->fetch();
    $stmt->close();
    return intval($sum);
}

/**
 * Closing stock at end of date (i.e., starting the next day) = opening + purchased - issued
 */
function closing_stock($product_id, $start, $end) {
    $open = opening_stock($product_id, $start);
    $p = purchased_between($product_id, $start, $end);
    $i = issued_between($product_id, $start, $end);
    return $open + $p - $i;
}
?>
