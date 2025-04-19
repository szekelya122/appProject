<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'modell/webshop.php'; // DB connection

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: ../frontend/cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$name = $address = $city = $zip = $country = '';

// If user clicked "use existing"
if (isset($_POST['use_existing']) && $_POST['use_existing'] == 1) {
    $stmt = $pdo->prepare("SELECT Name, address, city, zipcode, country FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $userShipping = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userShipping) {
        $name = $userShipping['Name'];
        $address = $userShipping['address'];
        $city = $userShipping['city'];
        $zip = $userShipping['zipcode'];
        $country = $userShipping['country'];
    }
} else {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $zip = $_POST['zip'] ?? '';
    $country = $_POST['country'] ?? '';
}

// Update user shipping info
try {
    $updateUser = $pdo->prepare("UPDATE users SET Name=?, address=?, city=?, zipcode=?, country=? WHERE user_id=?");
    $updateUser->execute([$name, $address, $city, $zip, $country, $user_id]);
} catch (PDOException $e) {
    die("Error updating user data: " . $e->getMessage());
}

// Process order
try {
    $pdo->beginTransaction();

    $orderStmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, quantity, order_date, status) VALUES (?, ?, ?, NOW(), 'Pending')");
    $updateStockStmt = $pdo->prepare("UPDATE product SET quantity = quantity - ? WHERE id = ?");

    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['product_id'], $item['quantity'])) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
    
            $orderResult = $orderStmt->execute([$user_id, $product_id, $quantity]);
            if (!$orderResult) {
                echo "Error inserting order for product ID: " . $product_id . "<br>";
                print_r($orderStmt->errorInfo()); // Get detailed error info
            }
    
            $stockResult = $updateStockStmt->execute([$quantity, $product_id]);
            if (!$stockResult) {
                echo "Error updating stock for product ID: " . $product_id . "<br>";
                print_r($updateStockStmt->errorInfo()); // Get detailed error info
            }
        }
    }

    $pdo->commit();
    unset($_SESSION['cart']);
    header("Location: ../front/confirmation.php");
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    die("❌ Order failed: " . $e->getMessage());
}
?>
