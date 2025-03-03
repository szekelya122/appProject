<?php
session_start(); 
include "../backend/modell/webshop.php";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle updating the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        if (isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity; // Update quantity
            } else {
                unset($_SESSION['cart'][$product_id]); // Remove item if quantity is 0
            }
        }
    }
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); // Remove item from cart
    }
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../front/cart.css">
    <title>Shopping Cart</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Your Cart</h1>
    <a href="shop.php">Continue Shopping</a> <!-- Link back to the shop -->
    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <form method="POST">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td>$<?= $item['price'] ?></td>
                        <td>
                            <input type="number" name="quantity[<?= $product_id ?>]" value="<?= $item['quantity'] ?>" min="1">
                        </td>
                        <td>$<?= $item['price'] * $item['quantity'] ?></td>
                        <td><a href="?remove=<?= $product_id ?>">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td colspan="2">$<?= $total ?></td>
                </tr>
            </table>
            <button type="submit" name="update_cart">Update Cart</button>
        </form>
    <?php endif; ?>
</body>
</html>