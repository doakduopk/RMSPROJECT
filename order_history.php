<?php
session_start();
include("includes/db.php");
include("includes/header.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: unauthorized.php");
    exit();
}

$current_user_id = (int)($_SESSION['user_id'] ?? 0);

$query = "SELECT
            o.order_id,
            o.status,
            o.order_date,
            d.quantity,
            COALESCE(r.name, 'Unknown Item') AS recipe_name,
            COALESCE(r.image_path, 'Assets/images') AS recipe_image
          FROM orders AS o
          LEFT JOIN order_details AS d ON o.order_id = d.order_id
          LEFT JOIN menu_items AS bound ON d.item_id = bound.item_id
          LEFT JOIN recipes AS r ON bound.recipe_id = r.recipe_id
          WHERE o.user_id = $current_user_id
          ORDER BY o.order_date DESC";

$orders = $conn->query($query);
?>

<main>
  <div class="history-container">
    <h1>Order History</h1>

    <?php
    if ($orders && $orders->num_rows > 0) {
        echo "<table class='order-history-table'>
                <tr class='table-header'>
                  <th>Image</th>
                  <th>Order ID</th>
                  <th>Item</th>
                  <th>Quantity</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Track</th>
                </tr>";

        while ($row = $orders->fetch_assoc()) {
            echo "<tr class='table-row'>
                    <td><img src='".htmlspecialchars($row['recipe_image'])."'
                             alt='".htmlspecialchars($row['recipe_name'])."'
                             class='item-img'></td>
                    <td>".$row['order_id']."</td>
                    <td>".htmlspecialchars($row['recipe_name'])."</td>
                    <td>".$row['quantity']."</td>
                    <td>".htmlspecialchars($row['status'])."</td>
                    <td>".$row['order_date']."</td>
                    <td><a href='deliveryTracking.php?order_id=".$row['order_id']."' class='track-link'>Track</a></td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<p class='no-orders-msg'>No past orders found.</p>";
    }
    ?>
  </div>
</main>

<?php include("includes/footer.php"); ?>