<?php
session_start();

if ($_SESSION['role'] != "admin") {
    echo "<script>alert('Admin page only for admins!'); window.location.href = '../front/index.php';</script>";
    exit();
}
include "../backend/manage_admin.php";
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

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
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <span class="badge bg-danger ms-2">
                            <?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?>
                        </span>
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
<?php if (!empty($feedbackMessage)) echo $feedbackMessage; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Admin Panel</h1>

    <div class="row">
        
        <div class="col-md-4">
            <h2>Upload New Product</h2>
            <form method="post" enctype="multipart/form-data" class="p-4 border rounded bg-light">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name:</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price:</label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                <label for="category_id" class="form-label">Category:</label>
        <select name="category_id" id="category_id" class="form-control" required>
            <option value="">-- Select Category --</option>
            <?php if (isset($categories) && !empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">No categories available</option>
            <?php endif; ?>
        </select>
        
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Product Image:</label>
                    <input type="file" name="image" id="image" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Upload Product</button>
            </form>
            </div>

        
<div class="col-md-4">
    <h2>Manage Products</h2>
    <ul class='list-group mt-3'>
        <?php
        $result = $pdo->query("SELECT * FROM product");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
            echo "<div class='d-flex align-items-center'>";
            
            echo "<strong style='margin-right: 10px;'>" . htmlspecialchars($row['name']) . "</strong>";
            echo "€" . number_format($row['price'], 2);
            echo "</div>";
            echo "<div class='d-flex'>";
            echo "<div class='btn-group' role='group' aria-label='Product actions'>";
            
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='delete_product_id' value='" . $row['id'] . "'>";
            echo "<button type='submit' class='btn btn-danger btn-sm'>Delete</button>";
            echo "</form>";
        
            echo "<button type='button' class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#updateProductModal" . $row['id'] . "'>Update Qty</button>";
            echo "</div>";
            echo "</div>";
            echo "</li>";

            
            echo "<div class='modal fade' id='updateProductModal" . $row['id'] . "' tabindex='-1' aria-labelledby='updateProductModalLabel" . $row['id'] . "' aria-hidden='true'>";
            echo "<div class='modal-dialog'>";
            echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
            echo "<h5 class='modal-title' id='updateProductModalLabel" . $row['id'] . "'>Update Quantity - " . htmlspecialchars($row['name']) . "</h5>";
            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
            echo "</div>";
            echo "<div class='modal-body'>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='update_product_id' value='" . $row['id'] . "'>";
            echo "<div class='mb-3'>";
            echo "<label for='new_quantity_" . $row['id'] . "' class='form-label'>New Quantity:</label>";
            echo "<input type='number' class='form-control' id='new_quantity_" . $row['id'] . "' name='new_quantity' value='" . $row['quantity'] . "' required>";
            echo "</div>";
            echo "<button type='submit' class='btn btn-primary w-100'>Update Quantity</button>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        ?>
    </ul>
</div>

        
        <div class="col-md-4">
            <h2>Manage Users</h2>
            <ul class='list-group mt-3'>
                <?php
                $result_users = $pdo->query("SELECT * FROM users");
                while ($user = $result_users->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                    echo "<strong>" . htmlspecialchars($user['username']) . "</strong> (ID: " . $user['user_id'] . ")";
                    echo "<form method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='delete_user_id' value='" . $user['user_id'] . "'>";
                    echo "<button type='submit' class='btn btn-danger btn-sm'>Delete</button>";
                    echo "</form>";
                    echo "</li>";
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>