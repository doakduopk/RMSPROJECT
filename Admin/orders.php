<?php
session_start();
include("../includes/db.php");
include("../includes/order_handler.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = trim($_POST['new_status']);

    if (updateOrderStatus($conn, $order_id, $new_status)) {
        echo "<p style='color:green; text-align:center;'>Order #$order_id updated to $new_status successfully.</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Failed to update order status.</p>";
    }
}

$query = "SELECT o.order_id, u.username, u.email, o.status AS order_status,
                 o.order_date, o.total_amount,
                 d.status AS delivery_status, d.gps_location
          FROM orders AS o
          INNER JOIN users AS u ON o.user_id = u.user_id
          LEFT JOIN delivery AS d ON o.order_id = d.order_id
          ORDER BY o.order_date DESC";
$result = $conn->query($query);
?>

<main>
  <div class="history-container">
    <h1>All Orders (Admin)</h1>
    <?php
    if ($result && $result->num_rows > 0) {
        echo "<table>
                <tr style='background-color:#f8f9fa;'>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Email</th>
                  <th>Order Status</th>
                  <th>Delivery Status</th>
                  <th>GPS Location</th>
                  <th>Date</th>
                  <th>Total</th>
                  <th>Action</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            $statusColor = match ($row['order_status']) {
                'pending' => '#ffc107',
                'preparing' => '#17a2b8',
                'out for delivery' => '#007bff',
                'delivered' => '#28a745',
                'cancelled' => '#dc3545',
                default => '#6c757d',
            };

            echo "<tr style='border-bottom:1px solid #ddd;'>
                    <td>{$row['order_id']}</td>
                    <td>".htmlspecialchars($row['username'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td><span style='color:$statusColor; font-weight:bold;'>".htmlspecialchars($row['order_status'])."</span></td>
                    <td>".htmlspecialchars($row['delivery_status'] ?? 'N/A')."</td>
                    <td>".htmlspecialchars($row['gps_location'] ?? 'N/A')."</td>
                    <td>{$row['order_date']}</td>
                    <td>$".htmlspecialchars($row['total_amount'])."</td>
                    <td>
                      <form method='POST' style='display:inline;'>
                        <input type='hidden' name='order_id' value='{$row['order_id']}'>
                        <select name='new_status'>
                          <option value='pending' ".($row['order_status']=='pending'?'selected':'').">Pending</option>
                          <option value='preparing' ".($row['order_status']=='preparing'?'selected':'').">Preparing</option>
                          <option value='out for delivery' ".($row['order_status']=='out for delivery'?'selected':'').">Out for Delivery</option>
                          <option value='delivered' ".($row['order_status']=='delivered'?'selected':'').">Delivered</option>
                          <option value='cancelled' ".($row['order_status']=='cancelled'?'selected':'').">Cancelled</option>
                        </select>
                        <button type='submit' style='background-color:#28a745; color:white; border:none; padding:5px 10px; border-radius:5px;'>Update</button>
                      </form>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align:center;'>No orders found.</p>";
    }
    ?>
  </div>
</main>

<?php include("../includes/footer.php"); ?>