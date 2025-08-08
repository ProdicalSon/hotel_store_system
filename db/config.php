<?php
// db/config.php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'hotel_store';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

/**
 * Safe escape
 */
function esc($v) {
    global $conn;
    return $conn->real_escape_string(trim($v));
}

/**
 * Convert date to Y-m-d
 */
function norm_date($d) {
    if(!$d) return date('Y-m-d');
    return date('Y-m-d', strtotime($d));
}
?>
