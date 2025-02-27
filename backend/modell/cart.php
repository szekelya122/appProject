<?php
// cart.php
include 'webshop.php';

class Cart {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addToCart($user_id, $product_id, $quantity) {
        $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $product_id, $quantity]);
    }
}
?>
