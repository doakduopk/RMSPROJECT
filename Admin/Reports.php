<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");

$totalOrders = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'] ?? 0;
$deliveredOrders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='delivered'")->fetch_assoc()['c'] ?? 0;
$totalSales = $conn->query("SELECT SUM(total_amount) AS s FROM orders WHERE status='delivered'")->fetch_assoc()['s'] ?? 0;
$topCustomer = $conn->query("SELECT users.username, SUM(total_amount) AS spent
                             FROM orders
                             JOIN users ON orders.user_id = users.user_id
                             WHERE orders.status='delivered'
                             GROUP BY users.username ORDER BY spent DESC LIMIT 1")->fetch_assoc();
?>
<main>
  <h1 style="text-align:center; color:#28a745; margin-bottom:30px;">Reports Dashboard</h1>

  <!-- Quick Stats -->
  <section class="stats-grid" >
    <div class="stat-card">
      <h3>Total Orders</h3>
      <p><?= $totalOrders ?></p>
    </div>
    <div class="stat-card">
      <h3>Delivered Orders</h3>
      <p><?= $deliveredOrders ?></p>
    </div>
    <div class="stat-card">
      <h3>Total Sales</h3>
      <p>$<?= number_format($totalSales, 2) ?></p>
    </div>
    <div class="stat-card">
      <h3>Top Customer</h3>
      <p><?= htmlspecialchars($topCustomer['username'] ?? 'N/A') ?> (<?= number_format($topCustomer['spent'] ?? 0, 2) ?>)</p>
    </div>
  </section>

  <!-- Daily Sales Trend -->
  <section class="report-section">
    <h2>Daily Sales Trend</h2>
    <canvas id="dailySalesChart"></canvas>
    <?php
    $days = [];
    $sales = [];
    $result = $conn->query("SELECT DATE(order_date) AS day, SUM(total_amount) AS daily_sales
                            FROM orders
                            WHERE status='delivered'
                            GROUP BY day ORDER BY day DESC LIMIT 7");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $days[] = $row['day'];
            $sales[] = $row['daily_sales'];
        }
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
      new Chart(dailySalesCtx, {
        type: 'line',
        data: {
          labels: <?php echo json_encode(array_reverse($days)); ?>,
          datasets: [{
            label: 'Daily Sales ($)',
            data: <?php echo json_encode(array_reverse($sales)); ?>,
            borderColor: '#007bff',
            fill: false
          }]
        }
      });
    </script>
  </section>

  <!-- Top Customers -->
  <section class="report-section">
    <h2>Top Customers</h2>
    <canvas id="topCustomersChart"></canvas>
    <?php
    $customers = [];
    $spent = [];
    $result = $conn->query("SELECT users.username, SUM(total_amount) AS spent
                            FROM orders
                            JOIN users ON orders.user_id = users.user_id
                            WHERE orders.status='delivered'
                            GROUP BY users.username ORDER BY spent DESC LIMIT 5");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row['username'];
            $spent[] = $row['spent'];
        }
    }
    ?>
    <script>
      const topCustomersCtx = document.getElementById('topCustomersChart').getContext('2d');
      new Chart(topCustomersCtx, {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($customers); ?>,
          datasets: [{
            label: 'Amount Spent ($)',
            data: <?php echo json_encode($spent); ?>,
            backgroundColor: '#28a745'
          }]
        }
      });
    </script>
  </section>
</main>



<?php include("../includes/footer.php"); ?>