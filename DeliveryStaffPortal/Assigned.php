<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: /RMSPROJECT/unauthorized.php");
    exit();
}

include("../includes/header.php");

$staff_id = $_SESSION['user_id'];

$sql = "SELECT d.delivery_id, d.status AS delivery_status, o.order_id, o.status AS order_status, o.order_date, o.total_amount
        FROM delivery d
        JOIN orders o ON d.order_id = o.order_id
        WHERE d.staff_id = $staff_id
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);
?>

<main>
  <div class="assigned-container">
    <h1>Assigned Orders</h1>
    <?php
    if (!$result) {
        echo "<p style='color:red;'>Database error: " . $conn->error . "</p>";
    } elseif ($result->num_rows > 0) {
        echo "<table>
                <tr>
                  <th>Delivery ID</th>
                  <th>Order ID</th>
                  <th>Order Status</th>
                  <th>Delivery Status</th>
                  <th>Total Amount</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['delivery_id']."</td>
                    <td>".$row['order_id']."</td>
                    <td>".$row['order_status']."</td>
                    <td>".$row['delivery_status']."</td>
                    <td>$".$row['total_amount']."</td>
                    <td>".$row['order_date']."</td>
                    <td><a href='update.php?delivery_id=".$row['delivery_id']."'>Update Status</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='no-orders'>No orders assigned yet.</p>";
    }
    ?>
  </div>
</main>

<?php include("../includes/footer.php"); ?>