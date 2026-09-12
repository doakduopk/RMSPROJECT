<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");

if (!isset($_GET['message_id'])) {
    echo "<p class='alert-error'>No message selected.</p>";
    include("../includes/footer.php");
    exit();
}

$message_id = (int)$_GET['message_id'];

$query = "SELECT * FROM contact_messages WHERE message_id = $message_id";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    echo "<p class='alert-error'>Message not found.</p>";
    include("../includes/footer.php");
    exit();
}

$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $reply = trim($conn->real_escape_string($_POST['reply']));
    $update = "UPDATE contact_messages SET reply = '$reply', replied_at = NOW() WHERE message_id = $message_id";

    if ($conn->query($update)) {
        echo "<main><div class='reply-success-box'>
                <p class='alert-success'>Reply sent successfully!</p>
                <p><a href='view_messages.php'>Back to Messages</a></p>
              </div></main>";
        include("../includes/footer.php");
        exit();
    } else {
        echo "<p class='alert-error'>Error saving reply: " . htmlspecialchars($conn->error) . "</p>";
    }
}
?>

<main>
  <div class='reply-container'>
    <h2>Reply to Message</h2>
    <p><strong>From:</strong> <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['email']) ?>)</p>
    <p><strong>Message:</strong> <?= htmlspecialchars($row['message']) ?></p>
    <form method='POST'>
      <textarea name='reply' rows='5' placeholder='Type your reply here...' required></textarea>
      <button type='submit'>Send Reply</button>
    </form>
  </div>
</main>

<?php include("../includes/footer.php"); ?>