<?php

// category.php
include 'webshop.php';

class Category {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllCategories() {
        $query = "CALL GetAllCategories()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesById() {
        $query = "CALL GetCategoriesById()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


// -------------------
// Tesztkód alul
// -------------------

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "Teszt fut...\n";

    // Teszt adatok
    $mockDataAll = [
        ['id' => 1, 'name' => 'Elektronika'],
        ['id' => 2, 'name' => 'Ruházat'],
    ];

    $mockDataById = [
        ['id' => 2, 'name' => 'Ruházat'],
    ];

    // Mock PDOStatement
    class MockStatement {
        private $data;
        public function __construct($data) {
            $this->data = $data;
        }
        public function execute() {}
        public function fetchAll($fetch_style) {
            return $this->data;
        }
    }

    // Mock PDO
    class MockPDO {
        public function prepare($query) {
            global $mockDataAll, $mockDataById;
            if (strpos($query, 'GetAllCategories') !== false) {
                return new MockStatement($mockDataAll);
            } elseif (strpos($query, 'GetCategoriesById') !== false) {
                return new MockStatement($mockDataById);
            }
            return new MockStatement([]);
        }
    }

    // Teszt objektum létrehozása
    $mockDb = new MockPDO();
    $category = new Category($mockDb);

    echo "1. getAllCategories teszt: ";
    $result = $category->getAllCategories();
    echo ($result === $mockDataAll ? "OK\n" : "HIBA\n");

    echo "2. getCategoriesById teszt: ";
    $result = $category->getCategoriesById();
    echo ($result === $mockDataById ? "OK\n" : "HIBA\n");
}
?>
