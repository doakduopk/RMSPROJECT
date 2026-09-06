<?php
session_start();
include("includes/header.php");
?>

<main>
  <div class="thankyou-container">
    <h1>Thank You!</h1>
    <p>Your order has been placed successfully.</p>
    <p>We’ll start preparing it right away.</p>
    <p>
      <a href="index.php">Back to Menu</a> |
      <a href="order_history.php">View Order History</a>
    </p>
  </div>
</main>

<?php include("includes/footer.php"); ?>