<?php
session_start();
include("includes/db.php");
include("includes/functions.php");
include("includes/order_handler.php");

if (!isset($_SESSION['user_id'])) {
    echo "Please <a href='login.php'>login</a> to place an order.";
    exit();
}

if (isset($_GET['item_id'])) {
    $item_id = (int)$_GET['item_id'];
    $user_id = $_SESSION['user_id'];

    if (placeOrder($conn, $user_id, $item_id, 1)) {
        updateInventory($conn, $item_id, 1);
        header("Location: thank_you.php");
        exit();
    } else {
        echo "Something went wrong placing the order.";
    }
} else {
    echo "No item selected.";
}
?>