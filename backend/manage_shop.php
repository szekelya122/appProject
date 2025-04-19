<?php
session_start(); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../backend/modell/webshop.php';

$errorMessage = null;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $errorMessage = "Nem vagy bejelentkezve. Jelentkezz be, hogy a kosárba tudj helyezni termékeket.";
}

$category_id = isset($_GET['category_id']) && $_GET['category_id'] !== ''
    ? intval($_GET['category_id'])
    : null;

if ($category_id) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name
        FROM product p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = :category_id
    ");
    $stmt->execute(['category_id' => $category_id]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, c.name AS category_name
        FROM product p
        LEFT JOIN categories c ON p.category_id = c.id
    ");
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle cart logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        $errorMessage = "Nem vagy bejelentkezve. Jelentkezz be, hogy a kosárba tudj helyezni termékeket.";
    } else {
        $prodId = intval($_POST['product_id']);
        $qty = intval($_POST['quantity']);
        $userId = $_SESSION['user_id'];

        $stmt = $pdo->prepare("
            SELECT quantity FROM cart 
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        $stmt->execute(['user_id' => $userId, 'product_id' => $prodId]);
        $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingProduct) {
            $newQuantity = $existingProduct['quantity'] + $qty;
            $updateStmt = $pdo->prepare("
                UPDATE cart SET quantity = :quantity 
                WHERE user_id = :user_id AND product_id = :product_id
            ");
            $updateStmt->execute([
                ':quantity' => $newQuantity,
                ':user_id' => $userId,
                ':product_id' => $prodId
            ]);
        } else {
            $insertStmt = $pdo->prepare("
                INSERT INTO cart (user_id, product_id, quantity, added_at) 
                VALUES (:user_id, :product_id, :quantity, NOW())
            ");
            $insertStmt->execute([
                ':user_id' => $userId,
                ':product_id' => $prodId,
                ':quantity' => $qty
            ]);
        }

        // Redirect after adding to cart
        $query = $_GET ? '?' . http_build_query($_GET) : '';
        header("Location: shop.php{$query}");
        exit;
    }
}
?>
