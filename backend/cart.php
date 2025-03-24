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
    header("Location: cart.php"); // Refresh the page to reflect changes
    exit();
}

// Handle removing items from the cart
if (isset($_GET['remove'])) {
    $product_id = filter_input(INPUT_GET, 'remove', FILTER_SANITIZE_NUMBER_INT);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); 
    }
    header("Location: cart.php"); 
    exit();
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
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="cart.css">
  
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>
        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
        
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
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $product_id ?>]" value="<?= $item['quantity'] ?>" min="1">
                            </td>
                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            <td><a href="?remove=<?= $product_id ?>" class="btn btn-danger">Remove</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3">Total</td>
                        <td colspan="2">$<?= number_format($total, 2) ?></td>
                    </tr>
                </table>
                <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
