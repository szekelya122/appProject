<?php
include_once "../backend/modell/webshop.php"; 


$userId = $_SESSION['user_id'] ?? null;
$cartDetails = [];

if ($userId) {
    try {
        $stmt = $pdo->prepare("
            SELECT c.product_id, c.quantity, p.name, p.price
            FROM cart c
            JOIN product p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$userId]);
        $cartDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Hiba a kosár lekérdezésénél: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $productId => $quantity) {
        $quantity = max(1, (int)$quantity); // Minimum 1 legyen
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $userId, $productId]);
    }

    header("Location: ../front/cart.php");
    exit;
}


if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $productId = (int)$_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);

    header("Location: cart.php");
    exit;
}
if (!empty($_SESSION['shipping']) && isset($_SESSION['user_id'])) {
    
    $cartItemsStmt = $pdo->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
    $cartItemsStmt->execute([$_SESSION['user_id']]);
    $cartItems = $cartItemsStmt->fetchAll(PDO::FETCH_ASSOC);

   
    foreach ($cartItems as $item) {
        $productId = $item['product_id'];
        $quantityOrdered = $item['quantity'];

        $updateProductStmt = $pdo->prepare("UPDATE product SET quantity = quantity - ? WHERE id = ?");
        $updateProductStmt->execute([$quantityOrdered, $productId]);
    }

    
   
}
?>
