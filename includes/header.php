<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . "/db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RECIPE MANAGEMENT SYSTEM PLUS ONLINE ORDERING</title>
  <link rel="stylesheet" href="/RMSPROJECT/css/style.css">

</head>

<body>

<header class="site-header">
  <nav>
    <h1>RECIPE MANAGEMENT SYSTEM PLUS ONLINE ORDERING</h1>


    <?php if (!empty($_SESSION['username']) && !empty($_SESSION['role'])): ?>
      <div class="welcome-message">
        <?php
          $role = $_SESSION['role'];
          $name = htmlspecialchars($_SESSION['username']);

          $emoji = match ($role) {
              'admin'  => '👑',
              'staff'  => '🚚',
              'chef'   => '👨‍🍳',
              'customer'  => '🍴',
          };

          $roleText = match ($role) {
              'admin' => 'Admin',
              'staff' => 'Delivery Staff',
              'chef'  => 'Chef',
              default => '',
          };

          echo $emoji . " Welcome" . ($roleText ? ", $roleText" : "") . " " . $name;
        ?>
      </div>
    <?php endif; ?>

    <ul class="header-list">
      
      <?php if (!empty($_SESSION['role'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <div><li><a href="/RMSPROJECT/Admin/menu.php">Manage Menu</a></li></div>
          <div><li><a href="/RMSPROJECT/Admin/recipes.php">Recipes</a></li></div>
          <div><li><a href="/RMSPROJECT/Admin/orders.php">Manage Orders</a></li></div>
          <div><li><a href="/RMSPROJECT/Admin/delivery.php">Assign Delivery</a></li></div>
          <div><li><a href="/RMSPROJECT/Admin/reports.php">View Reports</a></li></div>
          <div><li><a href="/RMSPROJECT/Admin/approve_recipe.php">Approve Recipes</a></li></div>
          <div><li><a href="/RMSPROJECT/Admin/view_messages.php">View Messages</a></li></div>
        <?php elseif ($_SESSION['role'] === 'staff'): ?>
          <div><li><a href="/RMSPROJECT/DeliveryStaffPortal/assigned.php">Assigned Deliveries</a></li></div>
        <?php elseif ($_SESSION['role'] === 'chef'): ?>
          <div><li><a href="/RMSPROJECT/Chef/add_recipe.php">Add Recipe</a></li></div>
          <div><li><a href="/RMSPROJECT/Chef/my_recipes.php">My Recipes</a></li></div>
        <?php elseif ($_SESSION['role'] === 'customer'): ?>
          <div><li><a href="/RMSPROJECT/index.php">Home</a></li></div>
          <div><li><a href="/RMSPROJECT/cart.php">Cart</a></li></div>
          <div><li><a href="/RMSPROJECT/order_history.php">Order History</a></li></div>
          <div><li><a href="/RMSPROJECT/contact.php">Contact</a></li></div>
        <?php endif; ?>
        <div><li><a href="/RMSPROJECT/logout.php">Log Out</a></li></div>
      <?php else: ?>
        <div><li><a href="/RMSPROJECT/index.php">Home</a></li></div>
        <div><li><a href="/RMSPROJECT/login.php">Login</a></li></div>
      <?php endif; ?>
      
    </ul>
  </nav>
</header>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

