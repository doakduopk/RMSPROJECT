<?php
include("../includes/db.php");
include("../includes/auth.php");
include("../includes/header.php");
?>
<main>
  <div class="gps-container">
    <h1>GPS Tracking</h1>
    <?php
    if (!isset($_GET['order_id'])) {
        echo "<p class='no-gps'>No order selected.</p>";
        include("../includes/footer.php");
        exit();
    }

    $order_id = (int)$_GET['order_id'];
    $result = $conn->query("SELECT gps_location FROM delivery WHERE order_id=$order_id");

    if ($result && $row = $result->fetch_assoc()) {
        if (!empty($row['gps_location'])) {
            echo "<p>Current GPS Location: <span class='gps-location'>" . $row['gps_location'] . "</span></p>";
        } else {
            echo "<p class='no-gps'>No GPS data available for this order.</p>";
        }
    } else {
        echo "<p class='no-gps'>No GPS data available for this order.</p>";
    }
    ?>
  </div>
</main>
<?php include("../includes/footer.php"); ?>