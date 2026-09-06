<?php
session_start();

require_once(__DIR__ . "/includes/db.php");
include(__DIR__ . "/includes/header.php");
?>
<main>
  <h2 style="color:#007bff;">Welcome to Our Hotel Restaurant</h2>
  <p>Browse our featured dishes or explore the full menu below.</p>
  <a href="order_history.php"> view your past orders </a>

  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <section class="admin-dashboard">
      <h3>Admin Dashboard</h3>
      <ul>
        <li><a href="Admin/recipes.php">Manage Recipes</a></li>
        <li><a href="Admin/menu.php">Manage Menu</a></li>
        <li><a href="Admin/orders.php">Manage Orders</a></li>
        <li><a href="Admin/delivery.php">Assign Delivery</a></li>
        <li><a href="Admin/reports.php">View Reports</a></li>
      </ul>
    </section>
  <?php endif; ?>

  <section class="featured">
    <h3>Featured Dishes</h3>
    <div class="featured-grid">
      <?php

      $sql = "SELECT maximum.item_id, maximum.price, r.name, r.ingredients, r.image_path
              FROM menu_items AS maximum
              INNER JOIN recipes AS r ON maximum.recipe_id = r.recipe_id
              WHERE r.status='approved'
              GROUP BY r.recipe_id
              ORDER BY maximum.item_id DESC
              LIMIT 3";
      $featured_items = $conn->query($sql);

      if ($featured_items && $featured_items->num_rows > 0) {
          while($row = $featured_items->fetch_assoc()) {
              echo "<div class='featured-item'>";
              echo "<img src='".htmlspecialchars($row['image_path'])."' alt='".htmlspecialchars($row['name'])."'>";
              echo "<h4>".htmlspecialchars($row['name'])."</h4>";
              echo "<p>".htmlspecialchars($row['ingredients'])."</p>";
              echo "<p><strong>$".htmlspecialchars($row['price'])."</strong></p>";
              echo "<a class='btn' href='cart.php?action=add&item_id=".$row['item_id']."'>Add to Cart</a>";
              echo "</div>";
          }
      } else {
          echo "<p>No approved menu items available right now.</p>";
      }
      ?>
    </div>
  </section>

  <section class="menu">
    <h3>Full Menu</h3>
    <?php

    $filter_clauses = ["r.status='approved'"];

    if (!empty($_GET['search'])) {
      $search_term = $conn->real_escape_string($_GET['search']);
      $filter_clauses[] = "r.name LIKE '%$search_term%'";
    }
    if (!empty($_GET['category'])) {
      $cat = $conn->real_escape_string($_GET['category']);
      $filter_clauses[] = "maximum.category='$cat'";
    }

    if (!empty($_GET['min_price'])) {
      $lowest = (float)$_GET['min_price'];
      $filter_clauses[] = "maximum.price >= $lowest";
    }
    if (!empty($_GET['max_price'])) {
      $peak = (float)$_GET['max_price'];
      $filter_clauses[] = "maximum.price <= $peak";
    }

    $where_sql = "WHERE " . implode(" AND ", $filter_clauses);

    $per_page = 10;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;
    $offset = ($current_page - 1) * $per_page;

    $count_sql = "SELECT COUNT(DISTINCT r.recipe_id) AS total
                   FROM menu_items AS maximum
                   INNER JOIN recipes AS r ON maximum.recipe_id = r.recipe_id
                   $where_sql";
    $count_res = $conn->query($count_sql);

    if (!$count_res) {
        echo "<p>Database error: " . htmlspecialchars($conn->error) . "</p>";
    } else {
        $data = $count_res->fetch_assoc();
        $total_items = (int)$data['total'];
        $total_pages = $total_items > 0 ? ceil($total_items / $per_page) : 1;

        if ($total_items > 0) {
            $query = "SELECT maximum.item_id, maximum.price, r.name, r.ingredients, r.image_path
                      FROM menu_items AS maximum
                      INNER JOIN recipes AS r ON maximum.recipe_id = r.recipe_id
                      $where_sql
                      GROUP BY r.recipe_id
                      ORDER BY maximum.item_id DESC
                      LIMIT $per_page OFFSET $offset";
            $items = $conn->query($query);

            echo "<div class='menu-grid'>";
            while($item = $items->fetch_assoc()) {
                echo "<div class='menu-item' >";
                echo "<img src='".htmlspecialchars($item['image_path'])."' alt='".htmlspecialchars($item['name'])."'>";
                echo "<h4>".htmlspecialchars($item['name'])."</h4>";
                echo "<p>".htmlspecialchars($item['ingredients'])."</p>";
                echo "<p><strong>$".htmlspecialchars($item['price'])."</strong></p>";
                echo "<a class='btn' href='cart.php?action=add&item_id=".$item['item_id']."'>Add to Cart</a>";
                echo "</div>";
            }
            echo "</div>";

            $start_num = $offset + 1;
            $end_num = min($offset + $per_page, $total_items);
            echo "<div class='pagination-info'>Showing {$start_num}–{$end_num} of {$total_items} items</div>";

            echo "<div class='pagination'>";
            if ($current_page > 1) {
                echo "<a href='?page=1'>First</a> ";
                echo "<a href='?page=".($current_page-1)."'>Prev</a> ";
            }
            for ($pos = 1; $pos <= $total_pages; $pos++) {
                echo ($pos == $current_page) ? "<strong>$pos</strong> " : "<a href='?page=$pos'>$pos</a> ";
            }
            if ($current_page < $total_pages) {
                echo "<a href='?page=".($current_page+1)."'>Next</a> ";
                echo "<a href='?page=$total_pages'>Last</a>";
            }
            echo "</div>";
        } else {
            echo "<p>No items found matching your criteria.</p>";
        }
    }
    ?>
  </section>
</main>

<?php include(__DIR__ . "/includes/footer.php"); ?>