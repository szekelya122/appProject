<?php
// user.php
include 'webshop.php';

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllUsers() {
        $query = "SELECT user_id, username, role, address, phonenumber, email FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUserById() {
        $query = "CALL GetUserById()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertUser($username, $role, $address, $phonenumber, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "CALL InsertUser(?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$username, $role, $address, $phonenumber, $email]);
    }
}

/***** TESTS *****/
/*
class UserTest extends PHPUnit\Framework\TestCase {
    private $user;
    private $pdo;

    protected function setUp(): void {
        $this->pdo = new PDO('mysql:host=localhost;dbname=webshop_test', 'root', 'root');
        $this->pdo->beginTransaction();
        $this->user = new User($this->pdo);
    }

    protected function tearDown(): void {
        $this->pdo->rollBack();
    }

    public function testGetAllUsers() {
        $this->pdo->exec("INSERT INTO users (username, email) VALUES ('testuser', 'test@example.com')");
        $users = $this->user->getAllUsers();
        $this->assertIsArray($users);
        $this->assertNotEmpty($users);
    }

    public function testGetUserById() {
        $this->pdo->exec("INSERT INTO users (username, email) VALUES ('testuser', 'test@example.com')");
        $users = $this->user->getUserById();
        $this->assertIsArray($users);
    }

    public function testInsertUser() {
        $result = $this->user->insertUser('newuser', 'user', '123 St', '123456', 'new@example.com', 'pass123');
        $this->assertTrue($result);
    }
}
*/
?>