<?php
session_start();
include("includes/db.php");
include("includes/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['role'])) {
    $username = trim($conn->real_escape_string($_POST['username']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $password = trim($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_email = $conn->query("SELECT user_id FROM users WHERE email='$email'");

    if ($check_email && $check_email->num_rows > 0) {
        echo "<p class='alert-msg-error'>Email already registered. <a href='login.php'>Login here</a></p>";
    } else {
        $sql = "INSERT INTO users (username, email, password, role, created_at)
                VALUES ('$username', '$email', '$hashed_password', '$role', NOW())";

        if ($conn->query($sql)) {
            echo "<p class='alert-msg-success'>Registration successful! <a href='login.php'>Login here</a></p>";
        } else {
            echo "<p class='alert-msg-error'>Error: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
}
?>

<main>
  <div class="login-container">
    <h2>Register</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      
      <select name="role" required>
        <option value="customer">Customer</option>
        <option value="staff">Staff</option>
        <option value="admin">Admin</option>
        <option value="chef">Chef</option>
      </select>
      
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
</main>

<?php include("includes/footer.php"); ?>