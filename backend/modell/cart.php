<?php

session_start();

function addToCart($product_id, $quantity) {
    global $conn;

    $sql = "SELECT name, price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
            ];
        }
    }
}

function updateCart($quantities) {
    foreach ($quantities as $product_id => $quantity) {
        if (isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    }
}

function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function getCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}


// -------------------
// Tesztkód alul
// -------------------

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "Teszt fut...\n";

    // Mock adatbázis és kapcsolat
    $mockDatabase = [
        1 => ['name' => 'TesztTermék1', 'price' => 1000],
        2 => ['name' => 'TesztTermék2', 'price' => 500],
    ];

    // Mock connection object
    $conn = new class {
        public function prepare($sql) {
            return new class {
                public function bind_param($type, $id) {
                    $this->id = $id;
                }
                public function execute() {}
                public function get_result() {
                    global $mockDatabase;
                    $id = $this->id;
                    return new class($id) {
                        private $id;
                        public function __construct($id) {
                            $this->id = $id;
                        }
                        public function fetch_assoc() {
                            global $mockDatabase;
                            return $mockDatabase[$this->id] ?? null;
                        }
                        public function __get($name) {
                            if ($name === 'num_rows') {
                                global $mockDatabase;
                                return isset($mockDatabase[$this->id]) ? 1 : 0;
                            }
                            return null;
                        }
                    };
                }
            };
        }
    };

    $_SESSION['cart'] = [];

    echo "1. Hozzáadás 2 db termékkel (id:1): ";
    addToCart(1, 2);
    echo ($_SESSION['cart'][1]['quantity'] === 2 ? "OK\n" : "HIBA\n");

    echo "2. Hozzáadás újabb 3 db ugyanabból: ";
    addToCart(1, 3);
    echo ($_SESSION['cart'][1]['quantity'] === 5 ? "OK\n" : "HIBA\n");

    echo "3. Frissítés 10 db-ra: ";
    updateCart([1 => 10]);
    echo ($_SESSION['cart'][1]['quantity'] === 10 ? "OK\n" : "HIBA\n");

    echo "4. Frissítés 0 db -> törlés: ";
    updateCart([1 => 0]);
    echo (!isset($_SESSION['cart'][1]) ? "OK\n" : "HIBA\n");

    echo "5. removeFromCart működik (id:2): ";
    addToCart(2, 1);
    removeFromCart(2);
    echo (!isset($_SESSION['cart'][2]) ? "OK\n" : "HIBA\n");

    echo "6. getCartTotal számítás: ";
    addToCart(1, 2); // 2*1000 = 2000
    addToCart(2, 3); // 3*500 = 1500
    $total = getCartTotal(); // 3500
    echo ($total === 3500 ? "OK\n" : "HIBA ($total)\n");
}
?>
