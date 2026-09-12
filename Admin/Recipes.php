<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");
include("../includes/header.php");
?>
<main class="recipes-main">
  <h1 class="recipes-title">Recipes</h1>
  <div class="recipes-container">
    <?php

    $query = "SELECT DISTINCT name, ingredients, steps
              FROM recipes
              WHERE status = 'approved'
              ORDER BY name ASC";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='recipe-card'>";
            echo "<h4 class='recipe-card-title'>".htmlspecialchars($row['name'])."</h4>";
            echo "<p><strong>Ingredients:</strong> ".htmlspecialchars($row['ingredients'])."</p>";
            echo "<p><strong>Steps:</strong> ".htmlspecialchars($row['steps'])."</p>";
            echo "</div>";
        }
    } else {
        echo "<p class='no-recipes-msg'>No approved recipes found.</p>";
    }
    ?>
  </div>
</main>

<?php include("../includes/footer.php"); ?>