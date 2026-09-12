<?php
session_start();
include("includes/db.php");
include("includes/functions.php");
include("includes/order_handler.php");
include("includes/auth.php");
include("includes/header.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: /RMSPROJECT/login.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]++;
    } else {
        $_SESSION['cart'][$item_id] = 1;
    }

    header("Location: cart.php");
    exit;
}


if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']);

    if (isset($_SESSION['cart'][$item_id])) {
        unset($_SESSION['cart'][$item_id]);
    }

    header("Location: cart.php");
    exit;
}
?>

<main>
  <div class="cart-container">
    <h1>Your Cart</h1>
    <?php
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "<p>Your cart is empty.</p>";
        echo "<a href='index.php' class='btn btn-secondary'>Browse Items</a>";
    } else {
        echo "<table class='cart-table'>
                <tr>
                  <th>Image</th>
                  <th>Item</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Total</th>
                  <th>Action</th>
                </tr>";
        $grand_total = 0;

        foreach ($_SESSION['cart'] as $item_id => $quantity) {
            $output = $conn->query("
                SELECT menu_items.price, recipes.name, recipes.image_path
                FROM menu_items
                JOIN recipes ON menu_items.recipe_id = recipes.recipe_id
                WHERE menu_items.item_id = $item_id
            ");

            if ($output && $output->num_rows > 0) {
                $item = $output->fetch_assoc();
                $total = $item['price'] * $quantity;
                $grand_total += $total;

                $img = !empty($item['image_path']) ? $item['image_path'] : "Assets/images/default.png";

                echo "<tr>
                        <td><img src='".htmlspecialchars($img)."' alt='".htmlspecialchars($item['name'])."' class='cart-item-img'></td>
                        <td>".htmlspecialchars($item['name'])."</td>
                        <td>".$quantity."</td>
                        <td>$".$item['price']."</td>
                        <td>$".$total."</td>
                        <td>
                          <a href='cart.php?action=remove&item_id=".$item_id."' class='btn btn-danger'>Delete</a>
                        </td>
                      </tr>";
            }
        }

        echo "</table>";
        echo "<h3 class='grand-total'>Grand Total: $".$grand_total."</h3>";
        
        echo "<div class='cart-actions'>
                <a href='index.php' class='btn btn-secondary'>Continue Shopping</a>
                <form method='POST' action='checkout.php' class='checkout-form'>
                  <button type='submit' class='btn btn-primary'>Proceed to Checkout</button>
                </form>
              </div>";
    }
    ?>
  </div>
</main>

<?php include("includes/footer.php"); ?>