<?php
session_start();

// Optional: Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szállítási adatok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center text-gold">Szállítási adatok</h1>
    <form action="payment.php" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Név</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Cím</label>
            <input type="text" class="form-control" name="address" required>
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">Város</label>
            <input type="text" class="form-control" name="city" required>
        </div>
        <div class="mb-3">
            <label for="zip" class="form-label">Irányítószám</label>
            <input type="text" class="form-control" name="zip" required>
        </div>
        <div class="mb-3">
            <label for="country" class="form-label">Ország</label>
            <input type="text" class="form-control" name="country" required>
        </div>
        <button type="submit" class="btn btn-gold w-100">Tovább a fizetéshez</button>
    </form>
</div>
</body>
</html>
