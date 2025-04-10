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

/***** TESTS *****/
/*
class OrderTest extends PHPUnit\Framework\TestCase {
    private $order;
    private $pdo;

    protected function setUp(): void {
        $this->pdo = new PDO('mysql:host=localhost;dbname=webshop_test', 'root', 'root');
        $this->pdo->beginTransaction();
        $this->order = new Order($this->pdo);
        
        // Setup test data
        $this->pdo->exec("INSERT INTO users (user_id, username) VALUES (1, 'testuser')");
        $this->pdo->exec("INSERT INTO product (product_id, name) VALUES (1, 'Test Product')");
    }

    protected function tearDown(): void {
        $this->pdo->rollBack();
    }

    public function testGetOrdersByUser() {
        $this->pdo->exec("INSERT INTO orders (user_id, product_id, quantity) VALUES (1, 1, 2)");
        $orders = $this->order->getOrdersByUser(1);
        $this->assertIsArray($orders);
        $this->assertNotEmpty($orders);
    }

    public function testPlaceOrder() {
        $result = $this->order->placeOrder(1, 1, 3);
        $this->assertTrue($result);
    }

    public function testGetOrderById() {
        $this->pdo->exec("INSERT INTO orders (user_id, product_id, quantity) VALUES (1, 1, 1)");
        $orders = $this->order->getOrderById();
        $this->assertIsArray($orders);
    }
}
*/
?>