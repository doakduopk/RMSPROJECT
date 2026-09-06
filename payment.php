<?php
session_start();
require 'vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;


Stripe::setApiKey('YOUR_SECRET_KEY');

if (!isset($_GET['amount']) || !is_numeric($_GET['amount'])) {
    echo "Invalid or missing amount.";
    exit();
}

$amount_in_cents = (int)round((float)$_GET['amount'] * 100);

$session = StripeSession::create([
  'payment_method_types' => ['card'],
  'line_items' => [[
    'price_data' => [
      'currency' => 'usd',
      'product_data' => ['name' => 'Hotel Food Order'],
      'unit_amount' => $amount_in_cents,
    ],
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => 'http://localhost/RMSPROJECT/success.php',
  'cancel_url' => 'http://localhost/RMSPROJECT/cancel.php'
]);

header("Location: " . $session->url);
exit();
?>
