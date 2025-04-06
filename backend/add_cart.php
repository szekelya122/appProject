<?php
session_start();

include "modell/webshop.php"; 

if (!isset($_SESSION["user_id"])) {
    echo ( "Felhasználó nincs bejelentkezve");
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["productId"], $data["quantity"])) {
    echo ( "Hiányzó adatok");
    exit();
}

$userId = $_SESSION["user_id"]; 
$productId = intval($data["productId"]);
$quantity = intval($data["quantity"]);

if ($quantity <= 0) {
    echo ("A mennyiségnek legalább 1-nek kell lennie.");
    exit();
}

$query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)
          ON DUPLICATE KEY UPDATE quantity = quantity + ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $userId, $productId, $quantity, $quantity);

if ($stmt->execute()) {
    http_response_code(200);
    echo ("Termék sikeresen hozzáadva a kosárhoz");
} else {
    http_response_code(404);
    echo ( "Adatbázis hiba");
}

$stmt->close();
$conn->close();
?>
