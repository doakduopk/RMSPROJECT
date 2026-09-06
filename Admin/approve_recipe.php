<?php
session_start();
include(__DIR__ . "/../includes/db.php");
include(__DIR__ . "/../includes/auth.php");



if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /RMSPROJECT/unauthorized.php");
    exit();
}

if (isset($_GET['id'], $_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
      
        $conn->query("UPDATE recipes SET status='approved' WHERE recipe_id=$id");

       
        $check = $conn->query("SELECT * FROM menu_items WHERE recipe_id=$id");
        if ($check && $check->num_rows === 0) {
          
            $conn->query("INSERT INTO menu_items (recipe_id, price, category, availability)
                          VALUES ($id, 0, 'Uncategorized', 1)");
        }
    } elseif ($action === 'reject') {
      
        $conn->query("UPDATE recipes SET status='rejected' WHERE recipe_id=$id");
    }
}


header("Location: manage_recipes.php");
exit();
?>
