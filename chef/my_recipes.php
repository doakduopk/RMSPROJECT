<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'chef') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");

$chef_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM recipes WHERE created_by = $chef_id ORDER BY recipe_id DESC");
?>

<main>
  <div class="recipe-list">
    <h2>My Recipes</h2>
    <table border="1" cellpadding="10" cellspacing="0">
      <tr>
        <th>ID</th>
        <th>Name</th>
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
          <td><?= htmlspecialchars($row['ingredients']) ?></td>
          <td><?= htmlspecialchars($row['steps']) ?></td>
          <td><?= htmlspecialchars($row['instructions']) ?></td>
          <td><img src="../<?= $row['image_path'] ?>" width="100"></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td>
            <?php if ($row['status'] === 'rejected'): ?>
              <a href="edit_recipe.php?id=<?= $row['recipe_id'] ?>" class="btn btn-warning">Edit</a>
            <?php else: ?>
              <span class="alert-msg-success">No Action</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</main>

<?php include("../includes/footer.php"); ?>