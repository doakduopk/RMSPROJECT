<?php
session_start();
require_once __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;


if ($order_id > 0) {
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Cancelled' WHERE order_id = ? AND payment_status = 'Pending'");
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
      <h1 class="text-warning mb-3">Payment Cancelled</h1>
      <p class="lead">Your payment process was cancelled and no charge was made.</p>
      
      <?php if ($order_id > 0): ?>
        <p><strong>Order Reference:</strong> #<?= htmlspecialchars($order_id) ?></p>
      <?php endif; ?>

      <hr class="my-4">
      <p>You can try checking out again or select a different payment method.</p>

      <div class="mt-4">
        <a href="cart.php" class="btn btn-primary me-2">Return to Cart</a>
        <a href="index.php" class="btn btn-outline-secondary">Back to Menu</a>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>