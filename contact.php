<?php
session_start();
require_once(__DIR__ . "/includes/db.php");
include(__DIR__ . "/includes/header.php");

$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? '';

if (!isset($_SESSION['viewed_reply_ids'])) {
    $_SESSION['viewed_reply_ids'] = array();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($conn->real_escape_string($_POST['name']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $message = trim($conn->real_escape_string($_POST['message']));
    
    $sql_user_id = $user_id ? (int)$user_id : "NULL";

    $sql = "INSERT INTO contact_messages (user_id, name, email, message, created_at)
            VALUES ($sql_user_id, '$name', '$email', '$message', NOW())";

    if ($conn->query($sql)) {
        $success_msg = "Thank you for contacting us! We’ll reply soon.";
    } else {
        $error_msg = "Error saving your message. Please try again later.";
    }
}


$viewed_ids_clause = "";
if (!empty($_SESSION['viewed_reply_ids'])) {
    $clean_ids = array_map('intval', $_SESSION['viewed_reply_ids']);
    $viewed_ids_clause = " OR id IN (" . implode(",", $clean_ids) . ") OR message_id IN (" . implode(",", $clean_ids) . ")";
}


$messages_query = null;
if ($user_id) {
    $messages_query = $conn->query("
        SELECT * FROM contact_messages 
        WHERE user_id = $user_id 
          AND (is_read_by_user = 0 $viewed_ids_clause)
        ORDER BY created_at DESC
    ");
} elseif (!empty($user_email)) {
    $escaped_email = $conn->real_escape_string($user_email);
    $messages_query = $conn->query("
        SELECT * FROM contact_messages 
        WHERE email = '$escaped_email' 
          AND (is_read_by_user = 0 $viewed_ids_clause)
        ORDER BY created_at DESC
    ");
}
?>

<main class="contact-main">
  <div class="contact-container">
    <h1>Contact Us</h1>

    <?php if (isset($success_msg)): ?>
      <p class="alert-success"><?= $success_msg ?></p>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
      <p class="alert-error"><?= $error_msg ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="name" placeholder="Your Name" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" required>
      <input type="email" name="email" placeholder="Your Email" value="<?= htmlspecialchars($user_email) ?>" required>
      <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
      <button type="submit">Send Message</button>
    </form>
  </div>

  <?php if ($messages_query && $messages_query->num_rows > 0): ?>
    <section class="user-replies-container">
      <h2>Your Support Messages & Replies</h2>
      <?php 
      while ($msg = $messages_query->fetch_assoc()): 
          $msg_id = $msg['id'] ?? $msg['message_id'] ?? null;

          if ($msg_id !== null) {
              if (!in_array($msg_id, $_SESSION['viewed_reply_ids'])) {
                  $_SESSION['viewed_reply_ids'][] = $msg_id;
              }

              if (!empty($msg['reply']) && $msg['is_read_by_user'] == 0) {
                  $id_col = isset($msg['message_id']) ? 'message_id' : 'id';
                  $conn->query("UPDATE contact_messages SET is_read_by_user = 1 WHERE $id_col = $msg_id");
              }
          }
      ?>
        <div class="message-reply-card">
          <p class="msg-sent"><strong>You sent:</strong> <?= htmlspecialchars($msg['message']) ?></p>
          <small class="msg-date">Sent on: <?= $msg['created_at'] ?></small>
          
          <?php if (!empty($msg['reply'])): ?>
            <div class="admin-reply-box">
              <p><strong>Admin Reply:</strong> <?= htmlspecialchars($msg['reply']) ?></p>
              <small class="reply-date">Replied on: <?= $msg['replied_at'] ?></small>
            </div>
          <?php else: ?>
            <p class="status-pending"><em>Status: Pending admin response...</em></p>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    </section>
  <?php endif; ?>
</main>

<?php include(__DIR__ . "/includes/footer.php"); ?>