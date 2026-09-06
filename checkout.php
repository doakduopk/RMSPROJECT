<?php
session_start();
include("includes/db.php");
include("includes/auth.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: /RMSPROJECT/unauthorized.php");
    exit();
}

include("includes/header.php");

$user_id = (int)($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_number = $conn->real_escape_string($_POST['card_number'] ?? '');
    $expiry = $conn->real_escape_string($_POST['expiry'] ?? '');
    $cvv = $conn->real_escape_string($_POST['cvv'] ?? '');

    if ($card_number && $expiry && $cvv) {
        if (!empty($_SESSION['cart'])) {
            $grand_total = 0;

            foreach ($_SESSION['cart'] as $item_id => $quantity) {
                $answer = $conn->query("SELECT price FROM menu_items WHERE item_id = $item_id");
                if ($answer && $row = $answer->fetch_assoc()) {
                    $grand_total += $row['price'] * $quantity;
                }
            }

            if (!isset($_SESSION['last_order_id'])) {
                $stmt = $conn->prepare("INSERT INTO orders (user_id, status, order_date, total_amount)
                                        VALUES (?, 'pending', NOW(), ?)");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("id", $user_id, $grand_total);
                $stmt->execute();
                $order_id = $stmt->insert_id;
                $_SESSION['last_order_id'] = $order_id;

                $item_stmt = $conn->prepare("INSERT INTO order_details (order_id, item_id, quantity, subtotal)
                                            VALUES (?, ?, ?, ?)");
                foreach ($_SESSION['cart'] as $item_id => $quantity) {
                    $result = $conn->query("SELECT price FROM menu_items WHERE item_id = $item_id");
                    if ($result && $row = $result->fetch_assoc()) {
                        $price = $row['price'];
                        $subtotal = $price * $quantity;
                        $item_stmt->bind_param("iiid", $order_id, $item_id, $quantity, $subtotal);
                        $item_stmt->execute();
                    }
                }

                unset($_SESSION['cart']);
            }

            echo "<main><div class='checkout-container'>
                    <p style='color:green;'>Payment successful! Your order #".$_SESSION['last_order_id']." has been placed.</p>
                    <p><a href='order_history.php'>View your order history</a></p>
                  </div></main>";
            include("includes/footer.php");
            exit();
        } else {
            echo "<main><div class='checkout-container'>
                    <p style='color:red;'>Your cart is empty.</p>
                  </div></main>";
        }
    } else {
        echo "<main><div class='Checkout-Container'>
                <p style='color:red;'>Please fill in all payment fields.</p>
              </div></main>";
    }
}
?>

<main>
  <div class='checkout-container'>
    <h1>Checkout</h1>
    <form method='POST'>
      <input type='text' name='card_number' placeholder='Card Number' required>
      <input type='text' name='expiry' placeholder='MM/YY' required>
      <input type='text' name='cvv' placeholder='CVV' required>
      <button type='submit'>Pay Now</button>
    </form>
    <p>Secure payment processing demo</p>
  </div>
</main>

<?php include("includes/footer.php"); ?>