<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
      <div class="welcome-message" style="text-align:right; margin:10px; font-weight:bold;">
        <?php
          $role = $_SESSION['role'];
          $name = htmlspecialchars($_SESSION['username']);

          $emoji = match ($role) {
              'admin'  => '👑',
              'staff'  => '🚚',
              'chef'   => '👨‍🍳',
              default  => '🍴',
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

    <ul>
      <?php if (!empty($_SESSION['role'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <li><a href="/RMSPROJECT/Admin/menu.php">Manage Menu</a></li>
          <li><a href="/RMSPROJECT/Admin/recipes.php">Recipes</a></li>
          <li><a href="/RMSPROJECT/Admin/orders.php">Manage Orders</a></li>
          <li><a href="/RMSPROJECT/Admin/delivery.php">Assign Delivery</a></li>
          <li><a href="/RMSPROJECT/Admin/reports.php">View Reports</a></li>
          <li><a href="/RMSPROJECT/Admin/approve_recipe.php">Approve Recipes</a></li>
          <li><a href="/RMSPROJECT/Admin/manage_recipes.php">Manage Recipes</a></li>
          <li><a href="/RMSPROJECT/Admin/view_messages.php">View Messages</a></li>
        <?php elseif ($_SESSION['role'] === 'staff'): ?>
          <li><a href="/RMSPROJECT/DeliveryStaffPortal/assigned.php">Assigned Deliveries</a></li>
        <?php elseif ($_SESSION['role'] === 'chef'): ?>
          <li><a href="/RMSPROJECT/Chef/add_recipe.php">Add Recipe</a></li>
          <li><a href="/RMSPROJECT/Chef/my_recipes.php">My Recipes</a></li>
        <?php elseif ($_SESSION['role'] === 'user'): ?>
          <li><a href="/RMSPROJECT/index.php">Home</a></li>
          <li><a href="/RMSPROJECT/cart.php">Cart</a></li>
          <li><a href="/RMSPROJECT/order_history.php">Order History</a></li>
          <li><a href="/RMSPROJECT/contact.php">Contact</a></li>
        <?php endif; ?>
        <li><a href="/RMSPROJECT/logout.php">Log<br>out</a></li>
      <?php else: ?>
        <li><a href="/RMSPROJECT/index.php">Home</a></li>
        <li><a href="/RMSPROJECT/login.php">Login</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

