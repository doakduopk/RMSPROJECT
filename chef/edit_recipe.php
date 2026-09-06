<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'chef') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $chef_id = $_SESSION['user_id'];

    $result = $conn->query("SELECT * FROM recipes WHERE recipe_id=$id AND created_by=$chef_id");
    $recipe = $result->fetch_assoc();

    if (!$recipe) {
        echo "<p style='color:red;'>Recipe not found or not yours.</p>";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $conn->real_escape_string($_POST['name']);
        $ingredients = $conn->real_escape_string($_POST['ingredients']);
        $steps = $conn->real_escape_string($_POST['steps']);
        $instructions = $conn->real_escape_string($_POST['instructions']);

        $sql = "UPDATE recipes SET
                name='$name',
                ingredients='$ingredients',
                steps='$steps',
                instructions='$instructions',
                status='pending'
                WHERE recipe_id=$id AND created_by=$chef_id";

        if ($conn->query($sql)) {
            echo "<p style='color:green;'>Recipe updated successfully! Resubmitted for admin approval.</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    }
}
?>

<main>
  <div class="recipe-container">
    <h2>Edit Recipe</h2>
    <form method="POST">
      <input type="text" name="name" value="<?= htmlspecialchars($recipe['name']) ?>" required>
      <textarea name="ingredients" required><?= htmlspecialchars($recipe['ingredients']) ?></textarea>
      <textarea name="steps" required><?= htmlspecialchars($recipe['steps']) ?></textarea>
      <textarea name="instructions" required><?= htmlspecialchars($recipe['instructions']) ?></textarea>
      <button type="submit">Update Recipe</button>
    </form>
  </div>
</main>

<?php include("../includes/footer.php"); ?>