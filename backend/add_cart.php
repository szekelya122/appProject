<?php
header("Content-Type: application/json");


include "modell/webshop.php";




$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["userId"], $data["productId"], $data["quantity"])) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit();
}

$userId = intval($data["user_id"]);
$productId = intval($data["id"]);
$quantity = intval($data["quantity"]);


$query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)
          ON DUPLICATE KEY UPDATE quantity = quantity + ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $userId, $productId, $quantity, $quantity);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Termék sikeresen hozzáadva a kosárhoz"]);
} else {
    echo json_encode(["success" => false, "error" => "Database error"]);
}

$stmt->close();
$conn->close();
?>
