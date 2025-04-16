<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kosár - Webshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
<?php
session_start();
?>  
<nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-gold">
    <div class="container">
        <a class="navbar-brand text-gold" href="../front/index.php">Webshop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="../front/index.php">Főoldal</a></li>
                <li class="nav-item"><a class="nav-link active text-gold" href="../front/shop.php">Bolt</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <a href="../front/cart.php" class="d-flex align-items-center me-3 text-decoration-none text-gold">
                    <i class="fas fa-shopping-cart text-white me-1"></i>
                    <span class="text-white">Kosár</span>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <span class="badge bg-danger ms-2">
                            <?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="admin.php" class="btn btn-outline-gold me-2">admin oldal</a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id']) ): ?>
                    <a href="../front/profile.php" class="text-white me-3 text-decoration-none">
                        <i class="bi bi-person-circle fs-3"></i>
                    </a>
                    <a href="../backend/logout.php" class="btn btn-outline-gold me-2">Kijelentkezés</a>
                <?php else: ?>
                    <a href="../front/logIn.php" class="btn btn-outline-gold me-2">Bejelentkezés</a>
                    <a href="../front/register.php" class="btn btn-gold">Regisztráció</a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</nav>

<main class="container mt-5">
    <h1 class="text-gold text-center">Kosár</h1>
    <a href="../front/shop.php" class="btn btn-outline-gold my-3">Vásárlás folytatása</a>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-warning text-center">A kosarad üres.</div>
    <?php else: ?>
        <form method="POST">
            <table class="table table-dark table-hover text-center">
                <thead>
                    <tr class="border-gold">
                        <th>Termék</th>
                        <th>Ár</th>
                        <th>Mennyiség</th>
                        <th>Összesen</th>
                        <th>Művelet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; ?>
                        <?php $total += $subtotal; ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $product_id ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control w-50 mx-auto">
                            </td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                            <td><a href="?remove=<?= $product_id ?>" class="btn btn-danger btn-sm">Eltávolítás</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-black text-gold">
                        <td colspan="3" class="text-end">Végösszeg:</td>
                        <td colspan="2">$<?= number_format($total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
            <button type="submit" name="update_cart" class="btn btn-gold w-100">Kosár frissítése</button>
        </form>
    <?php endif; ?>
</main>

<footer class="footer bg-dark text-white py-4 mt-auto">
    <div class="container text-center">
        <p>&copy; 2024 Webshop. Minden jog fenntartva.</p>
        <div class="social-links">
            <a href="https://facebook.com" class="text-white me-3">
                <i class="bi bi-facebook"></i> Facebook
            </a>
            <a href="https://twitter.com" class="text-white">
                <i class="bi bi-twitter"></i> Twitter
            </a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
