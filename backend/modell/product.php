<?php
// product.php
include 'webshop.php';

class Product {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts() {
        $query = "CALL GetAllProducts()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProductById() {
        $query = "CALL GetProductById()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductDetails($product_id) {
        $query = "CALL GetProductDetails(?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

