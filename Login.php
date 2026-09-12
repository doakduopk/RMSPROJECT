<?php
session_start();
require_once(__DIR__ . "/includes/db.php");

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user_data = $result->fetch_assoc();

        if (password_verify($password, $user_data['password'])) {
            $_SESSION['user_id'] = $user_data['user_id'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['role'] = $user_data['role'];
            $_SESSION['email'] = $user_data['email'] ?? '';

            $_SESSION['session_start_time'] = date('Y-m-d H:i:s');

            if ($user_data['role'] === 'admin') {
                header("Location: Admin/adminDashboard.php");
            } elseif ($user_data['role'] === 'staff') {
                header("Location: DeliveryStaffPortal/assigned.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "User not found.";
    }
}
?><?php
session_start();
require_once(__DIR__ . "/includes/db.php");

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user_data = $result->fetch_assoc();

        if (password_verify($password, $user_data['password'])) {
          
            $_SESSION['user_id'] = $user_data['user_id'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['role'] = $user_data['role'];
            $_SESSION['email'] = $user_data['email'] ?? '';
            
         
            $_SESSION['viewed_reply_ids'] = array();

            if ($user_data['role'] === 'admin') {
                header("Location: Admin/adminDashboard.php");
            } elseif ($user_data['role'] === 'staff') {
                header("Location: DeliveryStaffPortal/assigned.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "User not found.";
    }
}
?>

<?php include(__DIR__ . "/includes/header.php"); ?>
<main>
  <div class="login-container">
    <h2>Login</h2>
    <?php if ($login_error) echo "<p class='error-message'>$login_error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
  </div>
</main>
<?php include(__DIR__ . "/includes/footer.php"); ?>

<?php include(__DIR__ . "/includes/header.php"); ?>
<main>
  <div class="login-container">
    <h2>Login</h2>
    <?php if ($login_error) echo "<p class='error-message'>$login_error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
  </div>
</main>
<?php include(__DIR__ . "/includes/footer.php"); ?>