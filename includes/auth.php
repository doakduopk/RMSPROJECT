<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

 if (empty($_SESSION['user_id'])) {
 header("Location: /RMSPROJECT/login.php");
exit();
    }

$currentPath = $_SERVER['PHP_SELF'] ?? '';

if (str_contains($currentPath, '/Admin/') && ($_SESSION['role'] ?? '') !== 'admin') {
header("Location: /RMSPROJECT/unauthorized.php");
 exit();
 }

    if (str_contains($currentPath, '/DeliveryStaffPortal/') && ($_SESSION['role'] ?? '') !== 'staff') {
    header("Location: /RMSPROJECT/unauthorized.php");
exit();
}

 $customerAllowedPages = [
'/cart.php',
    '/checkout.php',
    '/order_history.php',
];

$customerRestricted = false;
 foreach ($customerAllowedPages as $page) {
 if (str_contains($currentPath, $page)) {
 $customerRestricted = true;
break;
    }
    }

if ($customerRestricted && ($_SESSION['role'] ?? '') !== 'customer') {
header("Location: /RMSPROJECT/unauthorized.php");
exit();
}