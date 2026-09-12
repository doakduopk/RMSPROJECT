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

// Fetch Daily Sales Data
$days = [];
$sales = [];
$resultSales = $conn->query("SELECT DATE(order_date) AS day, SUM(total_amount) AS daily_sales
                            FROM orders
                            WHERE status='delivered'
                            GROUP BY day ORDER BY day DESC LIMIT 7");
if ($resultSales && $resultSales->num_rows > 0) {
    while ($row = $resultSales->fetch_assoc()) {
        $days[] = $row['day'];
        $sales[] = $row['daily_sales'];
    }
}


$customers = [];
$spent = [];
$resultCustomers = $conn->query("SELECT users.username, SUM(total_amount) AS spent
                                FROM orders
                                JOIN users ON orders.user_id = users.user_id
                                WHERE orders.status='delivered'
                                GROUP BY users.username ORDER BY spent DESC LIMIT 5");
if ($resultCustomers && $resultCustomers->num_rows > 0) {
    while ($row = $resultCustomers->fetch_assoc()) {
        $customers[] = $row['username'];
        $spent[] = $row['spent'];
    }
}
?>

<main class="reports-main">
  <h1 class="dashboard-title">Reports Dashboard</h1>


  <section class="stats-grid">
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
      <p><?= htmlspecialchars($topCustomer['username'] ?? 'N/A') ?> ($<?= number_format($topCustomer['spent'] ?? 0, 2) ?>)</p>
    </div>
  </section>


  <section class="report-section">
    <h2>Daily Sales Trend</h2>
    <div class="chart-container">
      <canvas id="dailySalesChart"
              data-labels="<?php echo htmlspecialchars(json_encode(array_reverse($days))); ?>"
              data-values="<?php echo htmlspecialchars(json_encode(array_reverse($sales))); ?>">
      </canvas>
    </div>
  </section>


  <section class="report-section">
    <h2>Top Customers</h2>
    <div class="chart-container">
      <canvas id="topCustomersChart"
              data-labels="<?php echo htmlspecialchars(json_encode($customers)); ?>"
              data-values="<?php echo htmlspecialchars(json_encode($spent)); ?>">
      </canvas>
    </div>
  </section>
</main>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../Assets/js/reports-charts.js"></script>

<?php include("../includes/footer.php"); ?>