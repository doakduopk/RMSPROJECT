<?php

 function updateInventory($conn, $item_id, $quantity) {
 $item_id  = (int) $item_id;
 $quantity = (int) $quantity;

    if ($quantity <= 0 || $item_id <= 0) {
    return false;
}

        $sql = "UPDATE inventory
    SET stock_quantity = stock_quantity - $quantity
WHERE item_id = $item_id";

            return $conn->query($sql);
            }