<?php
session_start();
include("includes/db.php");
include("includes/auth.php");
include("includes/header.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: /RMSPROJECT/unauthorized.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    echo "<main><p style='color:red; text-align:center;'>No order selected.</p></main>";
    include("includes/footer.php");
    exit();
}

$order_id = (int)$_GET['order_id'];
$delivery_info = $conn->query("SELECT * FROM delivery WHERE order_id=$order_id");

echo "<main><div class='tracking-container'>";
echo "<h2>Delivery Tracking</h2>";

if ($delivery_info && $row = $delivery_info->fetch_assoc()) {
    echo "<p><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</p>";
    echo "<p><strong>Current Location:</strong> " . htmlspecialchars($row['gps_location']) . "</p>";
} else {
    echo "<p style='color:red;'>No delivery information found for this order. Maybe it hasn't shipped yet?</p>";
}

echo "</div></main>";

include("includes/footer.php");
?>