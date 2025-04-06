<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="logIn.css">

</head>
<body style="background-color: #121212; color: #fff;">

    
    <form method="post" action="../backend/login.php">
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
                    <!-- Profil ikon, ha be van jelentkezve -->
                    <a href="../front/profile.php" class="text-white me-3 text-decoration-none">
                        <i class="bi bi-person-circle fs-3"></i> <!-- Profil ikon -->
                    </a>
                    <!-- Kijelentkezés gomb, ha be van jelentkezve -->
                    <a href="../backend/logout.php" class="btn btn-outline-gold me-2">Kijelentkezés</a>
                <?php else: ?>
                    <!-- Ha nincs bejelentkezve, akkor jelenjen meg a bejelentkezés és regisztráció -->
                    <a href="../front/logIn.php" class="btn btn-outline-gold me-2">Bejelentkezés</a>
                    <a href="../front/register.php" class="btn btn-gold">Regisztráció</a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container p-4 bg-dark border rounded border-gold">
                    <h2 class="text-center text-gold mb-4">Bejelentkezés</h2>

               

                        <div class="mb-3">
                            <input type="text" name="username" class="form-control bg-black text-white border-gold" placeholder="Felhasználónév" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control bg-black text-white border-gold" placeholder="Jelszó" required>
                        </div>

                        <button type="submit" class="btn btn-gold w-100">Bejelentkezés</button>
                    </form>
                  
                    <div class="text-center mt-3">
                        <span>Nincs még fiókod? </span>
                        <a href="register.php" class="text-gold text-decoration-none">Regisztráció</a>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
