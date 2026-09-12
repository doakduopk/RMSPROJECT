<?php
session_start();
require_once __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$session_id = $_GET['session_id'] ?? '';

// Clear cart after successful payment
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}


if ($order_id > 0) {
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid', status = 'Processing' WHERE order_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<main class="container my-5">
  <div class="card text-center p-4 shadow-sm" style="max-width: 600px; margin: 0 auto;">
    <div class="card-body">
      <h1 class="text-success mb-3">Payment Successful!</h1>
      <p class="lead">Thank you for your order. Your payment has been processed successfully.</p>
      
      <?php if ($order_id > 0): ?>
        <p><strong>Order ID:</strong> #<?= htmlspecialchars($order_id) ?></p>
      <?php endif; ?>

      <hr class="my-4">
      <p>Your food order is now being prepared by our kitchen.</p>
      
      <div class="mt-4">
        <a href="order_history.php" class="btn btn-primary me-2">View Order History</a>
        <a href="index.php" class="btn btn-outline-secondary">Return to Menu</a>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>