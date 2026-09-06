<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");

$result = $conn->query("SELECT r.*, u.username AS chef_name
                        FROM recipes r
                        LEFT JOIN users u ON r.created_by = u.user_id
                        ORDER BY r.recipe_id DESC");
?>

<main>
  <div class="recipe-list">
    <h2>Manage Recipes</h2>
    <table border="1" cellpadding="10" cellspacing="0">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Chef</th>
        <th>Ingredients</th>
        <th>Steps</th>
        <th>Instructions</th>
        <th>Image</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['recipe_id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['chef_name']) ?></td>
          <td><?= htmlspecialchars($row['ingredients']) ?></td>
          <td><?= htmlspecialchars($row['steps']) ?></td>
          <td><?= htmlspecialchars($row['instructions']) ?></td>
          <td><img src="../<?= $row['image_path'] ?>" width="100"></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td>
            <?php if ($row['status'] === 'pending'): ?>
              <a href="approve_recipe.php?id=<?= $row['recipe_id'] ?>&action=approve" style="color:green;">Approve</a> |
              <a href="approve_recipe.php?id=<?= $row['recipe_id'] ?>&action=reject" style="color:red;">Reject</a>
            <?php else: ?>
              <span style="color:gray;">No Action</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</main>

<?php include("../includes/footer.php"); ?>