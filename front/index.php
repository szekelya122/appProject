<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fedezze fel az exkluzív termékeket és az időtlen dizájnokat a Luxurious Webshopban. Vásároljon most a legjobb ajánlatokért.">
    <title>Webshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <script src="news.js"></script>

</head>
<body>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        if (getQueryParam('register') === 'success') {
            Swal.fire({
                title: 'Success!',
                text: 'User registered successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.replaceState(null, null, window.location.pathname);
            });
        }

        if (getQueryParam('login') === 'success') {
            Swal.fire({
                title: 'Success!',
                text: 'Logged in successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.replaceState(null, null, window.location.pathname);
            });
        }
    </script>
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
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
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



    <main>
        <section class="hero text-center py-5 main">
            <div class="container back">
                <h1 class="display-4">Üdvözlünk a Webshopban</h1>
                <p class="lead">Fedezze fel az exkluzív termékeket és az időtlen dizájnokat.</p>
                <a href="../front/shop.php" class="btn btn-primary btn-lg text-white text-decoration-none">Vásárlás most</a>
            </div>
        </section>

        <div class="container mt-5">
            <h2 class="text-gold">Friss hírek</h2>
            <div id="news-container" class="mt-3"></div>
        </div>
        
        <script src="news.js"></script>
        
    </main>


<footer class="footer bg-dark text-white py-4">
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
