<?php
session_start(); 
include "../backend/modell/webshop.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle updating the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $quantity = (int)$quantity; // Ensure it's an integer
        if (isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity; 
            } else {
                unset($_SESSION['cart'][$product_id]); 
            }
        }
    }
    header("Location: ../front/cart.php"); // Refresh the page to reflect changes
    exit();
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $product_id = filter_input(INPUT_GET, 'remove', FILTER_SANITIZE_NUMBER_INT);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); 
    }
    header("Location: ../front/cart.php"); 
    exit();
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

