<?php
session_start(); 
session_regenerate_id(true); 

include "../backend/modell/webshop.php";

// Database connection
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Fetch products securely
$stmt = $conn->prepare("SELECT id, name, price, img_path, quantity FROM product");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('You aren\'t logged in yet!');</script>";
    } else {
        $product_id = (int) $_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

        $product = array_filter($products, fn($p) => $p['id'] == $product_id);
        $product = reset($product);

        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                ];
            }
        }
    }
}
?>