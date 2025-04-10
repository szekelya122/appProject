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

/***** TESTS *****/
/*
class ProductTest extends PHPUnit\Framework\TestCase {
    private $product;
    private $pdo;

    protected function setUp(): void {
        $this->pdo = new PDO('mysql:host=localhost;dbname=webshop_test', 'root', 'root');
        $this->pdo->beginTransaction();
        $this->product = new Product($this->pdo);
        
        $this->pdo->exec("INSERT INTO product (product_id, name, price) VALUES (1, 'Test Product', 9.99)");
    }

    protected function tearDown(): void {
        $this->pdo->rollBack();
    }

    public function testGetAllProducts() {
        $products = $this->product->getAllProducts();
        $this->assertIsArray($products);
        $this->assertNotEmpty($products);
    }

    public function testGetProductById() {
        $products = $this->product->getProductById();
        $this->assertIsArray($products);
    }

    public function testGetProductDetails() {
        $product = $this->product->getProductDetails(1);
        $this->assertIsArray($product);
        $this->assertEquals('Test Product', $product['name']);
    }
}
*/
?>