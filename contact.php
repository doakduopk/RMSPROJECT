<?php
session_start();
require_once(__DIR__ . "/includes/db.php");
include(__DIR__ . "/includes/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($conn->real_escape_string($_POST['name']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $message = trim($conn->real_escape_string($_POST['message']));

    $sql = "INSERT INTO contact_messages (name, email, message, created_at)
            VALUES ('$name', '$email', '$message', NOW())";

    if ($conn->query($sql)) {
        echo "<main><div class='contact-container'>
                <p style='color:green;'>Thank you for contacting us! We’ll reply soon.</p>
                <p><a href='index.php'>Back to Home</a></p>
              </div></main>";
        include(__DIR__ . "/includes/footer.php");
        exit();
    } else {
        echo "<main><div class='contact-container'>
                <p style='color:red;'>Error saving your message. Please try again later.</p>
                <p style='color:#6c757d;'>Debug info: " . htmlspecialchars($conn->error) . "</p>
              </div></main>";
    }
}
?>

<main>
  <div class="contact-container">
    <h1>Contact Us</h1>
    <form method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
      <button type="submit">Send Message</button>
    </form>
    <p>We’ll get back to you as soon as possible.</p>
  </div>
</main>

<?php include(__DIR__ . "/includes/footer.php"); ?>