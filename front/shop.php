<?php
session_start(); 
include_once "../backend/manage_shop.php";
require_once "../backend/modell/webshop.php";

// Initialize cart quantity
$totalQuantity = 0;

// If user is logged in, fetch cart quantity
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_quantity FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalQuantity = $row['total_quantity'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termékek - Webshop</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shop.css">
    <link rel="stylesheet" href="index.css">
</head>
<body style="background-color: #121212; color: #fff;">

<nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-gold shadow">
    <div class="container">
        <a class="navbar-brand text-gold" href="../front/index.php">Webshop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="../front/index.php">Főoldal</a></li>
                <li class="nav-item"><a class="nav-link" href="../front/shop.php">Bolt</a></li>
            </ul>

            <div class="d-flex align-items-center">
                <a href="../front/cart.php" class="d-flex align-items-center me-3 text-decoration-none text-gold">
                    <i class="fas fa-shopping-cart text-white me-1"></i>
                    <span class="text-white">Kosár</span>
                    <?php if ($totalQuantity > 0): ?>
                        <span class="badge bg-danger ms-2"><?= $totalQuantity ?></span>
                    <?php endif; ?>
                </a>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="admin.php" class="btn btn-outline-gold me-2 active text-gold">Admin oldal</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../backend/logout.php" class="btn btn-outline-gold me-2">Kijelentkezés</a>
                <?php else: ?>
                    <a href="../front/logIn.php" class="btn btn-outline-gold me-2">Bejelentkezés</a>
                    <a href="../front/register.php" class="btn btn-gold">Regisztráció</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<section class="py-5">
    <div class="container">
        <h1 class="text-center mb-4 text-gold">Termékeink</h1>
        <div class="row">
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mx-auto">
                        <select name="category_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Összes kategória</option>
                            <?php
                            $stmt = $pdo->query("SELECT id, name FROM categories");
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category):
                                $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : '';
                            ?>
                                <option value="<?= $category['id'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?= htmlspecialchars($product['img_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text">Ár: €<?= number_format($product['price'], 2) ?></p>
                            <p class="text-warning">Elérhető mennyiség: <?= $product['quantity'] ?> db</p>
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" class="form-control mb-2">
                                <button type="submit" name="add_to_cart" class="btn btn-gold">Kosárba</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<footer class="py-3 bg-black text-center border-top border-gold">
    <p class="mb-0">&copy; 2024 Webshop. Minden jog fenntartva. | <a href="#" class="text-gold">Adatvédelem</a></p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarNav = document.querySelector('#navbarNav');

        if (navbarToggler && navbarNav) {
            navbarToggler.addEventListener('click', function() {
                navbarNav.classList.toggle('show');
            });
        }
    });
</script>
</body>
</html>
