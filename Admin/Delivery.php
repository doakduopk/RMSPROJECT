<?php
session_start();
include(__DIR__ . "/../includes/db.php");
include(__DIR__ . "/../includes/order_handler.php");
include(__DIR__ . "/../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include(__DIR__ . "/../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['staff_id'])) {
    $order_id = (int)$_POST['order_id'];
    $staff_id = (int)$_POST['staff_id'];

    if (assignDeliveryStaff($conn, $order_id, $staff_id)) {
        updateOrderStatus($conn, $order_id, "out for delivery");
        echo "<p style='color:green; text-align:center;'>Order #$order_id assigned to Staff #$staff_id successfully.</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Failed to assign delivery staff.</p>";
    }
}

$query = "SELECT o.order_id, o.status, o.order_date, o.total_amount,
                 u.username AS customer
          FROM orders AS o
          JOIN users AS u ON o.user_id = u.user_id
          WHERE o.status IN ('pending', 'preparing')
          ORDER BY o.order_date DESC";
$result = $conn->query($query);

$staffResult = $conn->query("SELECT user_id, username FROM users WHERE role='staff'");
$staffOptions = [];
if ($staffResult && $staffResult->num_rows > 0) {
    while ($row = $staffResult->fetch_assoc()) {
        $staffOptions[$row['user_id']] = $row['username'];
    }
}
?>

<main>
  <div class="delivery-container">
    <h1>Assign Delivery Staff</h1>
    <?php
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0' >
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Total</th>
                  <th>Assign Staff</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['order_id']."</td>
                    <td>".htmlspecialchars($row['customer'])."</td>
                    <td>".htmlspecialchars($row['status'])."</td>
                    <td>".$row['order_date']."</td>
                    <td>$".$row['total_amount']."</td>
                    <td>
                      <form method='POST' style='display:inline;'>
                        <input type='hidden' name='order_id' value='".$row['order_id']."'>
                        <select name='staff_id' required>";
            foreach ($staffOptions as $id => $username) {
                echo "<option value='$id'>".htmlspecialchars($username)."</option>";
            }
            echo "    </select>
                        <button type='submit'>Assign</button>
                      </form>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No pending orders available for assignment.</p>";
    }
    ?>
  </div>
</main>

<?php include(__DIR__ . "/../includes/footer.php"); ?>