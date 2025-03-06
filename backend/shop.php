<?php
session_start(); // Start the session to manage the cart

include "modell/webshop.php";

// Fetch products from the database
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$result = $conn->query("SELECT * FROM product");
$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'], // Ensure the product ID is included
            'name' => $row['name'],
            'price' => $row['price'],
            'img_path' => $row['img_path']
        ];
    }
}

$conn->close();

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Find the product in the $products array
    $product = null;
    foreach ($products as $p) {
        if ($p['id'] == $product_id) {
            $product = $p;
            break;
        }
    }

    if ($product) {
        // Initialize the cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add the product to the cart or update the quantity
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
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fedezze fel exkluzív prémium termékeinket. Vásároljon most, és tapasztalja meg a luxust.">
    <title>Termékek - Webshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../front/shop.css      ">
</head>
<body style="background-color: #121212; color: #fff;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-gold">
        <div class="container">
            <a class="navbar-brand text-gold" href="../front/index.html">Webshop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Navigáció megnyitása">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="../front/index.html">Főoldal</a></li>
                    <li class="nav-item"><a class="nav-link active text-gold" href="../backend/shop.php">Bolt</a></li>
                    
                </ul>
                <div class="d-flex align-items-center">
                    <a href="cart.php" class="d-flex align-items-center me-3 text-decoration-none text-gold">
                        <i class="fas fa-shopping-cart text-white me-1"></i>
                        <span class="text-white">Kosár</span>
                    </a>
                    <a href="../front/logIn.html" class="btn btn-outline-gold me-2">Bejelentkezés</a>
                    <a href="../front/register.html" class="btn btn-gold">Regisztráció</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Shop Section -->
    <section class="py-5">
        <div class="container">
            <h1 class="text-center mb-4 text-gold">Termékeink</h1>

            <!-- Product List -->
            <div class="row g-4" id="productList">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-dark text-white">
                            <img src="<?= htmlspecialchars($product['img_path']) ?>" class="card-img-top cardimage"  alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text">Price: €<?= number_format($product['price'], 2) ?></p>
                                <!-- Add to Cart Form -->
                                <form method="POST" action="">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                                    <button type="submit" name="add_to_cart" b class="btn btn-gold">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-3 bg-black text-center border-top border-gold">
        <p class="mb-0">&copy; 2024 Webshop. Minden jog fenntartva. | <a href="#" class="text-gold">Adatvédelem</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>