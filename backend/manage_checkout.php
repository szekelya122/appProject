<?php
session_start();
require_once '../backend/modell/webshop.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$hasShippingInfo = false;
$name = $address = $city = $zip = $country = '';
$totalAmount = 0.00;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    
    $stmt = $pdo->prepare("SELECT Name, address, city, zipcode, country FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData && !empty($userData['address'])) {
        $hasShippingInfo = true;
        $name = $userData['Name'];
        $address = $userData['address'];
        $city = $userData['city'];
        $zip = $userData['zipcode'];
        $country = $userData['country'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['use_existing'])) {
        if ($hasShippingInfo) {
            $_SESSION['shipping'] = [
                'name' => $userData['Name'],
                'address' => $userData['address'],
                'city' => $userData['city'],
                'zip' => $userData['zipcode'],
                'country' => $userData['country']
            ];
        } else {
            $error = 'Nincs mentett cím a profilodban.';
        }
    }
   
    elseif (isset($_POST['new_address'])) {
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $zip = trim($_POST['zip']);
        $country = trim($_POST['country']);

        if ($name && $address && $city && $zip && $country) {
            $_SESSION['shipping'] = [
                'name' => $name,
                'address' => $address,
                'city' => $city,
                'zip' => $zip,
                'country' => $country
            ];

            if (isset($_POST['save_address'])) {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, address = ?, city = ?, zipcode = ?, country = ? WHERE user_id = ?");
                $stmt->execute([$name, $address, $city, $zip, $country, $userId]);
            }
        } else {
            $error = 'Kérlek, tölts ki minden mezőt!';
        }
    }

    
    if (!empty($_SESSION['shipping']) && isset($_SESSION['user_id'])) {
        
        $cartStmt = $pdo->prepare("SELECT SUM(p.price * c.quantity) AS total_amount
                                   FROM cart c
                                   JOIN product p ON c.product_id = p.id
                                   WHERE c.user_id = ?");
        $cartStmt->execute([$_SESSION['user_id']]);
        $cartData = $cartStmt->fetch(PDO::FETCH_ASSOC);

        $totalAmount = $cartData['total_amount'] ?? 0;

       
        $orderDate = date('Y-m-d H:i:s');
        $shipping = $_SESSION['shipping'];

        $stmt = $pdo->prepare("INSERT INTO orders (
            user_id, order_date, status, total_amount, 
            shipping_name, shipping_address, shipping_city, shipping_zip, shipping_country
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_SESSION['user_id'],
            $orderDate,
            'pending',
            $totalAmount,
            $shipping['name'],
            $shipping['address'],
            $shipping['city'],
            $shipping['zip'],
            $shipping['country']
        ]);

        $orderId = $pdo->lastInsertId();

        
        $cartItemsStmt = $pdo->prepare("
            SELECT c.product_id, c.quantity, p.price
            FROM cart c
            JOIN product p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $cartItemsStmt->execute([$_SESSION['user_id']]);
        $cartItems = $cartItemsStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            echo "⚠️ Nem található termék a kosárban!";
            exit;
        }

        foreach ($cartItems as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, ordered_price)
                                   VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                echo " Hiba order_items beszúrásakor: " . $errorInfo[2] . "<br>";
            }
        }

        $clearCartStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearCartStmt->execute([$_SESSION['user_id']]);

        header('Location: confirmation.php');
        exit;
    }
}
?>
