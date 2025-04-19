<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../backend/modell/webshop.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    
    $stmt = $pdo->prepare("CALL GetUserLastFiveOrders(?)");
    $stmt->execute([$user_id]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();


    $shippingStmt = $pdo->prepare("SELECT name, address, city, zipcode, country FROM users WHERE user_id = ?");
    $shippingStmt->execute([$user_id]);
    $shipping = $shippingStmt->fetch(PDO::FETCH_ASSOC);

   
    $ordersWithItems = [];
    foreach ($orderItems as $item) {
        $orderId = $item['order_id'];
        if (!isset($ordersWithItems[$orderId])) {
            $ordersWithItems[$orderId] = [
                'order_date' => $item['order_date'],
                'status' => $item['status'],
                'items' => [],
                'total' => 0
            ];
        }
        $lineTotal = $item['ordered_price'] * $item['quantity'];
        $ordersWithItems[$orderId]['items'][] = [
            'product_name' => $item['product_name'],
            'quantity' => $item['quantity'],
            'ordered_price' => $item['ordered_price'],
            'line_total' => $lineTotal
        ];
        $ordersWithItems[$orderId]['total'] += $lineTotal;
    }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit;
}
?>
