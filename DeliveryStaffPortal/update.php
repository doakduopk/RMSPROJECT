<?php
include("../includes/db.php");
include("../includes/auth.php");
include("../includes/header.php");

$delivery_id = isset($_GET['delivery_id']) ? (int)$_GET['delivery_id'] : 0;
?>
<main>
  <div class="update-container">
    <h1>Update Delivery Status</h1>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $conn->real_escape_string($_POST['status']);
        $gps = $conn->real_escape_string($_POST['gps']);
        $sql = "UPDATE delivery SET status='$status', gps_location='$gps' WHERE delivery_id=$delivery_id";
        if ($conn->query($sql)) {
            echo "<p style='color:green;'>Delivery updated successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error: ".$conn->error."</p>";
        }
    }
    ?>
    <form method="POST">
      <select name="status" required>
        <option value="">Select Status</option>
        <option value="out_for_delivery">Out for Delivery</option>
        <option value="delivered">Delivered</option>
      </select>
      <input type="text" name="gps" placeholder="Current GPS Location" required>
      <button type="submit">Update</button>
    </form>
  </div>
</main>
<?php include("../includes/footer.php"); ?>