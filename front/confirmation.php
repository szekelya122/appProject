<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../backend/modell/webshop.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the latest orders (you can limit this to just today’s orders, or most recent 5 orders)
$stmt = $pdo->prepare("
SELECT o.order_date, o.status, p.name AS product_name, p.price 
FROM orders o
JOIN product p ON o.product_id = p.id
WHERE o.user_id = ?
ORDER BY o.order_date DESC
LIMIT 5
");

$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch shipping info
$shippingStmt = $pdo->prepare("SELECT Name, address, city, zipcode, country FROM users WHERE user_id = ?");
$shippingStmt->execute([$user_id]);
$shipping = $shippingStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Rendelés visszaigazolás</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .text-gold {
            color: #c9a634;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-gold">Köszönjük a rendelésed!</h1>
        <p class="text-center">Az alábbi rendelést rögzítettük:</p>

        <div class="card mt-4">
            <div class="card-header bg-gold text-white">
                Szállítási adatok
            </div>
            <div class="card-body">
                <p><strong>Név:</strong> <?= htmlspecialchars($shipping['Name']) ?></p>
                <p><strong>Cím:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= htmlspecialchars($shipping['zipcode']) ?>, <?= htmlspecialchars($shipping['country']) ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-gold text-white">
                Rendelés részletei
            </div>
            <div class="card-body">
                <?php if ($orders): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Termék</th>
                                <th>Ár (db)</th>
                                <th>Ár összesen</th>
                                <th>Dátum</th>
                                <th>Állapot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                                    <td><?= number_format($order['price'], 0, ',', ' ') ?> Ft</td>
                                    <td><?= number_format($order['price'], 0, ',', ' ') ?> Ft</td>
                                    <td><?= date("Y.m.d H:i", strtotime($order['order_date'])) ?></td>
                                    <td><?= htmlspecialchars($order['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nincs elérhető rendelési adat.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-outline-primary">Vissza a főoldalra</a>
        </div>
    </div>
</body>
</html>
