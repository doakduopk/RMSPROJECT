<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recipe_id'])) {
    $recipe_id = (int)$_POST['recipe_id'];
    $price = (float)$_POST['price'];
    $category = $conn->real_escape_string($_POST['category']);
    $availability = isset($_POST['availability']) ? 1 : 0;

    $check = $conn->query("SELECT * FROM menu_items WHERE recipe_id=$recipe_id");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE menu_items
                      SET price=$price, category='$category', availability=$availability
                      WHERE recipe_id=$recipe_id");
    } else {
        $conn->query("INSERT INTO menu_items (recipe_id, price, category, availability)
                      VALUES ($recipe_id, $price, '$category', $availability)");
    }

    header("Location: menu.php?updated=1");
    exit();
}
?>
<main>
  <h1 style="text-align:center; color:#28a745; margin-bottom:30px;">Manage Menu</h1>

  <?php
  if (isset($_GET['updated'])) {
      echo "<p style='text-align:center; color:green;'>Menu item saved successfully! It now appears on the customer page.</p>";
  }
  ?>

  <div class="menu-grid">
    <?php

    $query = "SELECT r.recipe_id, r.name AS recipe_name, r.ingredients, r.image_path,
                     COALESCE(peak.price, 0) AS price,
                     COALESCE(peak.category, 'Uncategorized') AS category,
                     COALESCE(peak.availability, 0) AS availability
              FROM recipes AS r
              LEFT JOIN menu_items AS peak ON r.recipe_id = peak.recipe_id
              WHERE r.status = 'approved'
              GROUP BY r.recipe_id
              ORDER BY r.name ASC";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='menu-card'>";
            echo "<img src='../".htmlspecialchars($row['image_path'])."' alt='".htmlspecialchars($row['recipe_name'])."'>";
            echo "<h4>".htmlspecialchars($row['recipe_name'])."</h4>";
            echo "<p>".htmlspecialchars($row['ingredients'])."</p>";

            echo "<form method='POST'>
                    <input type='hidden' name='recipe_id' value='".$row['recipe_id']."'>
                    <label>Category:</label>
                    <input type='text' name='category' value='".htmlspecialchars($row['category'])."' required>
                    <label>Price:</label>
                    <input type='number' step='0.01' name='price' value='".htmlspecialchars($row['price'])."' required>
                    <label><input type='checkbox' name='availability' ".($row['availability'] ? "checked" : "")."> Available</label>
                    <button type='submit'>Save</button>
                  </form>";

            echo "</div>";
        }
    } else {
        echo "<p>No approved recipes available.</p>";
    }
    ?>
  </div>
</main>

<?php include("../includes/footer.php"); ?>