<?php

require '../backend/manage_confirmation.php';


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
        .bg-gold {
            background-color: #c9a634;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center text-gold">Köszönjük a rendelésed!</h1>
    <p class="text-center">Az alábbi rendeléseket rögzítettük:</p>

    <!-- Szállítási adatok -->
    <div class="card mt-4">
        <div class="card-header bg-gold text-white">
            Szállítási adatok
        </div>
        <div class="card-body">
            <p><strong>Név:</strong> <?= htmlspecialchars($shipping['name']) ?></p>
            <p><strong>Cím:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= htmlspecialchars($shipping['zipcode']) ?>, <?= htmlspecialchars($shipping['country']) ?></p>
        </div>
    </div>

    <!-- Rendelések megjelenítése -->
    <?php if (!empty($ordersWithItems)): ?>
        <?php foreach ($ordersWithItems as $orderId => $orderData): ?>
            <div class="card mt-4">
                <div class="card-header bg-gold text-white">
                    Rendelés #<?= htmlspecialchars($orderId) ?> | Dátum: <?= htmlspecialchars($orderData['order_date']) ?> | Állapot: <?= htmlspecialchars($orderData['status']) ?> | Végösszeg: <?= number_format($orderData['total'], 0, ',', ' ') ?> €
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Termék</th>
                                <th>Darab</th>
                                <th>Ár/db</th>
                                <th>Összesen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderData['items'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name'] ?? 'Ismeretlen termék') ?></td>
                                    <td><?= htmlspecialchars($item['quantity'] ?? 1) ?></td>
                                    <td><?= number_format($item['ordered_price'], 0, ',', ' ') ?> €</td>
                                    <td><?= number_format($item['line_total'], 0, ',', ' ') ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card mt-4">
            <div class="card-body">
                <p>Nincs elérhető rendelési adat.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-outline-primary">Vissza a főoldalra</a>
    </div>
</div>
</body>
</html>
