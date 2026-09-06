<?php
session_start();
include("../includes/db.php");
include("../includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

include("../includes/header.php");

if (isset($_GET['message_id'])) {
    $message_id = (int)$_GET['message_id'];
    $conn->query("UPDATE contact_messages SET replied_at = NOW() WHERE message_id = $message_id");
}

$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<main>
  <div class="messages-container" >
    <h2>Customer Messages</h2>
    <?php
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0' >
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Message</th>
                  <th>Reply</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['message_id']."</td>
                    <td>".htmlspecialchars($row['name'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>".htmlspecialchars($row['message'])."</td>
                    <td>".(!empty($row['reply']) ? htmlspecialchars($row['reply']) : "<span>No reply yet</span>")."</td>
                    <td>".$row['created_at']."</td>
                    <td><a href='reply_message.php?message_id=".$row['message_id']."'>Reply</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No messages found.</p>";
    }
    ?>
  </div>
</main>

<?php include("../includes/footer.php"); ?>