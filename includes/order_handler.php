<?php

 function placeOrder($conn, $user_id, $item_id, $quantity, $address = null, $phone = null, $notes = null) {
 $user_id  = (int)$user_id;
$item_id  = (int)$item_id;
    $quantity = (int)$quantity;

$priceResult = $conn->query("SELECT price FROM menu_items WHERE item_id = $item_id");
    $price = ($priceResult && $row = $priceResult->fetch_assoc()) ? (float)$row['price'] : 0;
    $total_amount = $price * $quantity;

$sql = "INSERT INTO orders (user_id, status, order_date, total_amount)
    VALUES ($user_id, 'pending', NOW(), $total_amount)";

            if (!$conn->query($sql)) {
return false;
    }

    $order_id = $conn->insert_id;

$conn->query("
    INSERT INTO orders_detail (order_id, item_id, quantity, subtotal)
    VALUES ($order_id, $item_id, $quantity, $total_amount)
        ");

if ($address !== null && $phone !== null) {
    $safe_address = $conn->real_escape_string($address);

        $conn->query("
INSERT INTO delivery (order_id, staff_id, status, gps_location, assigned_date)
        VALUES ($order_id, NULL, 'pending', '$safe_address', NOW())
            ");
            }

    return true;
}

function updateOrderStatus($conn, $order_id, $new_status, $gps_location = null) {
$order_id = (int)$order_id;

 $new_status = trim($conn->real_escape_string($new_status));
$gps_location = $gps_location ? $conn->real_escape_string($gps_location) : null;

$valid_statuses = ['pending', 'preparing', 'out for delivery', 'delivered', 'cancelled'];

    if (!in_array(strtolower($new_status), $valid_statuses, true)) {
return false;
    }

    $conn->query("UPDATE orders SET status = '$new_status' WHERE order_id = $order_id");

    $gps_sql = $gps_location ? ", gps_location = '$gps_location'" : "";
$conn->query("UPDATE delivery SET status = '$new_status' $gps_sql WHERE order_id = $order_id");

return ($conn->affected_rows >= 0);
    }

    function assignDeliveryStaff($conn, $order_id, $staff_id) {
$order_id = (int)$order_id;
$staff_id = (int)$staff_id;

 $check = $conn->query("SELECT delivery_id FROM delivery WHERE order_id = $order_id");

    if ($check && $check->num_rows > 0) {
    $sql = "UPDATE delivery
SET staff_id = $staff_id,
    status = 'out for delivery',
    assigned_date = NOW()
WHERE order_id = $order_id";
    } else {
        $sql = "INSERT INTO delivery (order_id, staff_id, status, gps_location, assigned_date)
                VALUES ($order_id, $staff_id, 'out for delivery', '', NOW())";
                    }

                $success = $conn->query($sql);

        if ($success) {
                $conn->query("UPDATE orders SET status = 'out for delivery' WHERE order_id = $order_id");
    }

    return $success;
}