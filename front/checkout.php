<?php
session_start();
require_once '../backend/modell/webshop.php'; // Ensure this connects to your database
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$error = '';
$hasShippingInfo = false;
$name = $address = $city = $zip = $country = '';
$totalAmount = 0.00; // You should calculate this based on the user's cart items

// Check if user is logged in and has saved shipping info
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Retrieve the user's address details from the `users` table
    $stmt = $pdo->prepare("SELECT Name, address, city, zipcode, country FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData && !empty($userData['address'])) {
        $hasShippingInfo = true;
        // Populate form fields with existing user data
        $name = $userData['Name'];
        $address = $userData['address'];
        $city = $userData['city'];
        $zip = $userData['zipcode'];
        $country = $userData['country'];
    }
}

// Handle form submission for new address or existing address
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['use_existing'])) {
        // Use existing address from `users` table
        if ($hasShippingInfo) {
            $_SESSION['shipping'] = [
                'name' => $userData['Name'],
                'address' => $userData['address'],
                'city' => $userData['city'],
                'zip' => $userData['zipcode'],
                'country' => $userData['country']
            ];
        } else {
            $error = 'Nincs mentett cím a profilodban.';
        }

    } elseif (isset($_POST['new_address'])) {
        // Get new shipping info from form
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $zip = trim($_POST['zip']);
        $country = trim($_POST['country']);

        if ($name && $address && $city && $zip && $country) {
            // Store the new address in session for the order
            $_SESSION['shipping'] = [
                'name' => $name,
                'address' => $address,
                'city' => $city,
                'zip' => $zip,
                'country' => $country
            ];

            // Optionally save the new address to the `users` table if checkbox is checked
            if (isset($_POST['save_address']) && isset($_SESSION['user_id'])) {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, address = ?, city = ?, zipcode = ?, country = ? WHERE user_id = ?");
                $stmt->execute([$name, $address, $city, $zip, $country, $userId]);
            }
        } else {
            $error = 'Kérlek, tölts ki minden mezőt!';
        }
    }

    // Now insert into `orders` table after shipping information is set
    if (!empty($_SESSION['shipping']) && isset($_SESSION['user_id'])) {
        // Calculate total amount (e.g., sum of cart items)
        // Assuming you have a `cart` table, you can calculate it like this:
        $cartStmt = $pdo->prepare("SELECT SUM(p.price * c.quantity) AS total_amount
                                  FROM cart c
                                  JOIN product p ON c.product_id = p.id
                                  WHERE c.user_id = ?");
        $cartStmt->execute([$_SESSION['user_id']]);
        $cartData = $cartStmt->fetch(PDO::FETCH_ASSOC);

        if ($cartData) {
            $totalAmount = $cartData['total_amount']; // Get the total amount from the cart
        }

        // Insert the order into the `orders` table
        $orderDate = date('Y-m-d H:i:s'); // Current date and time
        $shippingName = $_SESSION['shipping']['name'];
        $shippingAddress = $_SESSION['shipping']['address'];
        $shippingCity = $_SESSION['shipping']['city'];
        $shippingZip = $_SESSION['shipping']['zip'];
        $shippingCountry = $_SESSION['shipping']['country'];

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, total_amount, shipping_name, shipping_address, shipping_city, shipping_zip, shipping_country)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'], 
            $orderDate, 
            $totalAmount, 
            $shippingName, 
            $shippingAddress, 
            $shippingCity, 
            $shippingZip, 
            $shippingCountry
        ]);

        // Get the last inserted order ID
        $orderId = $pdo->lastInsertId();

        // Insert the items from the cart into the `order_items` table
        $cartItemsStmt = $pdo->prepare("SELECT c.product_id, c.quantity, p.price
                                       FROM cart c
                                       JOIN product p ON c.product_id = p.id
                                       WHERE c.user_id = ?");
        $cartItemsStmt->execute([$_SESSION['user_id']]);
        $cartItems = $cartItemsStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cartItems as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price)
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
        }

        // Optionally, clear the cart after placing the order
        $clearCartStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $clearCartStmt->execute([$_SESSION['user_id']]);

        // Redirect to a confirmation page or payment page
        header('Location: confirmation.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szállítási adatok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Webshop Neve</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Főoldal</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Termékek</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Kosár </a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Kijelentkezés</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Bejelentkezés</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Regisztráció</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center text-gold">Szállítási adatok</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($hasShippingInfo): ?>
            <div class="alert alert-info" id="existingAddressAlert">
                Úgy tűnik, korábban megadott szállítási címet. Szeretnéd használni?
                <form method="POST" action="">
                    <input type="hidden" name="use_existing" value="1">
                    <button type="submit" class="btn btn-sm btn-outline-success">Igen, használom a meglévőt</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('newAddressForm').style.display = 'block'; document.getElementById('existingAddressAlert').style.display = 'none';">Nem, új címet adok meg</button>
                </form>
            </div>
            <form action="" method="POST" id="newAddressForm" style="display: none;">
                <input type="hidden" name="new_address" value="1">
                <h2 class="mt-4 text-center text-gold">Új szállítási cím megadása</h2>
                <div class="mb-3">
                    <label for="name" class="form-label">Név</label>
                    <input type="text" class="form-control" name="name" value="<?= $name ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Cím</label>
                    <input type="text" class="form-control" name="address" value="<?= $address ?>" required>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">Város</label>
                    <input type="text" class="form-control" name="city" value="<?= $city ?>" required>
                </div>
                <div class="mb-3">
                    <label for="zip" class="form-label">Irányítószám</label>
                    <input type="text" class="form-control" name="zip" value="<?= $zip ?>" required>
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Ország</label>
                    <input type="text" class="form-control" name="country" value="<?= $country ?>" required>
                </div>
                <button type="submit" class="btn btn-gold w-100">Tovább a fizetéshez</button>
            </form>
        <?php else: ?>
            <form action="" method="POST">
                <input type="hidden" name="new_address" value="1">
                <div class="mb-3">
                    <label for="name" class="form-label">Név</label>
                    <input type="text" class="form-control" name="name" value="<?= $name ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Cím</label>
                    <input type="text" class="form-control" name="address" value="<?= $address ?>" required>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">Város</label>
                    <input type="text" class="form-control" name="city" value="<?= $city ?>" required>
                </div>
                <div class="mb-3">
                    <label for="zip" class="form-label">Irányítószám</label>
                    <input type="text" class="form-control" name="zip" value="<?= $zip ?>" required>
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Ország</label>
                    <input type="text" class="form-control" name="country" value="<?= $country ?>" required>
                </div>
                
                <button type="submit" class="btn btn-gold w-100">Tovább a fizetéshez</button>
                <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="save_address" value="1" id="saveAddress">
    <label class="form-check-label" for="saveAddress">
        Cím mentése a profilomba
    </label>
</div>

            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>