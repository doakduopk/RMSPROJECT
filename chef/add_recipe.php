<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'chef') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['ingredients'], $_POST['steps'], $_POST['instructions'], $_FILES['image'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $ingredients = $conn->real_escape_string($_POST['ingredients']);
    $steps = $conn->real_escape_string($_POST['steps']);
    $instructions = $conn->real_escape_string($_POST['instructions']);

    $targetDir = "../Assets/images/";
    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $imagePath = "Assets/images/" . $imageName;

        $sql = "INSERT INTO recipes (name, ingredients, steps, instructions, image_path, status, created_by)
                VALUES ('$name', '$ingredients', '$steps', '$instructions', '$imagePath', 'pending', '{$_SESSION['user_id']}')";

        if ($conn->query($sql)) {
            echo "<p class='alert-success'>Recipe submitted successfully! Awaiting admin approval.</p>";
        } else {
            echo "<p class='alert-error'>Error: " . htmlspecialchars($conn->error) . "</p>";
        }
    } else {
        echo "<p class='alert-error'>Image upload failed.</p>";
    }
}
?>

<main>
  <div class="recipe-container">
    <h2>Add New Recipe</h2>
    <form method="POST" enctype="multipart/form-data" style="display:flex; border-radius: 12px; ;width: 500px; ;flex-direction: column; gap:15px;">
      <input type="text" name="name" placeholder="Recipe Name" required>
      <textarea name="ingredients" placeholder="Ingredients (comma separated)" required></textarea>
      <textarea name="steps" placeholder="Preparation Steps" required></textarea>
      <textarea name="instructions" placeholder="Cooking Instructions" required></textarea>
      <input type="file" name="image" accept="image/*" required>
      <button type="submit">Submit Recipe</button>
    </form>
  </div>
</main>

<?php include("../includes/footer.php"); ?>