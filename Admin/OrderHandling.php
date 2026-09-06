<?php
session_start();
include("../includes/db.php");
include("../includes/functions.php");
include("../includes/order_handler.php");
include("../includes/auth.php");
include("../includes/header.php");
?>
<main>
  <h1>Order Handling</h1>
  <?php
  if (!isset($_GET['item_id'])) {
    echo "<p>No item selected.</p>";
    include("../includes/footer.php");
    exit();
  }

  $item_id = (int)$_GET['item_id'];
  $user_id = $_SESSION['user_id'];

  $sql = "INSERT INTO orders (user_id, item_id, quantity, status) VALUES ($user_id, $item_id, 1, 'pending')";
  if ($conn->query($sql)) {
      updateInventory($conn, $item_id, 1);
      echo "<p>Order placed successfully!</p>";
  } else {
      echo "<p>Error: " . $conn->error . "</p>";
  }
  ?>
</main>
<?php include("../includes/footer.php"); ?>