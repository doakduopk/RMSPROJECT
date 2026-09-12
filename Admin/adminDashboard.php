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
$pendingOrders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='pending' OR status='preparing'")->fetch_assoc()['c'] ?? 0;
$deliveredOrders = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='delivered'")->fetch_assoc()['c'] ?? 0;
$approvedRecipes = $conn->query("SELECT COUNT(*) AS c FROM recipes WHERE status='approved'")->fetch_assoc()['c'] ?? 0;
$pendingRecipes = $conn->query("SELECT COUNT(*) AS c FROM recipes WHERE status='pending'")->fetch_assoc()['c'] ?? 0;
$staffCount = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='staff'")->fetch_assoc()['c'] ?? 0;
?>
<main>
  <h1 style="text-align:center; color:#007bff; margin-bottom:30px;">Admin Dashboard</h1>
<section class="stat-Grid">
  <div class="stat-card total-orders">
    <h3>Total Orders</h3>
    <p><?= $totalOrders ?></p>
  </div>
  <div class="stat-card pending-orders">
    <h3>Pending Orders</h3>
    <p><?= $pendingOrders ?></p>
  </div>
  <div class="stat-card delivered-orders">
    <h3>Delivered Orders</h3>
    <p><?= $deliveredOrders ?></p>
  </div>
  <div class="stat-card approved-recipes">
    <h3>Approved Recipes</h3>
    <p><?= $approvedRecipes ?></p>
  </div>
  <div class="stat-card pending-recipes">
    <h3>Pending Recipes</h3>
    <p><?= $pendingRecipes ?></p>
  </div>
  <div class="stat-card delivery-staff">
    <h3>Delivery Staff</h3>
    <p><?= $staffCount ?></p>
  </div>
</section>


</main>

<?php include("../includes/footer.php"); ?>