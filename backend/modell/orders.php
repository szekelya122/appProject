<?php
// orders.php
include 'webshop.php';

class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getOrdersByUser($user_id) {
        $query = "CALL GetOrdersByUser(?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function placeOrder($user_id, $product_id, $quantity) {
        $query = "CALL PlaceOrder(?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $product_id, $quantity]);
    }
    public function getOrderById() {
        $query = "CALL GetOrderById()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>