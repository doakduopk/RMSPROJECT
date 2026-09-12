<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;


Stripe::setApiKey('YOUR_SECRET_KEY');

if (!isset($_GET['amount']) || !is_numeric($_GET['amount'])) {
    echo "Invalid or missing amount.";
    exit();
}

$amount = (float)$_GET['amount'];
$amount_in_cents = (int)round($amount * 100);
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;


$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . $host . "/RMSPROJECT";

try {
    $session = StripeSession::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Hotel Food Order' . ($order_id ? " #$order_id" : ''),
                ],
                'unit_amount' => $amount_in_cents,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $base_url . '/success.php?session_id={CHECKOUT_SESSION_ID}' . ($order_id ? "&order_id=$order_id" : ''),
        'cancel_url' => $base_url . '/cancel.php' . ($order_id ? "?order_id=$order_id" : '')
    ]);

    header("Location: " . $session->url);
    exit();
} catch (Exception $e) {
    echo "Error creating payment session: " . htmlspecialchars($e->getMessage());
    exit();
}
?>