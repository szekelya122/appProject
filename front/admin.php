<?php
session_start();

if ($_SESSION['role'] != "admin") {
    echo "<script>alert('admin page only for admins !'); window.location.href = '../front/index.php';</script>";
    exit(); // Megállítjuk a további végrehajtást
};

include "../backend/modell/webshop.php";

// Function to delete a product
function deleteProduct($pdo, $product_id) {
    $stmt = $pdo->prepare("DELETE FROM product WHERE id = :id");
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Function to delete a user (assuming you have a user table)
function deleteUser($pdo, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id"); // Assuming 'users' and 'user_id'
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle product deletion
        if (isset($_POST['delete_product_id'])) {
            $product_id_to_delete = filter_var($_POST['delete_product_id'], FILTER_VALIDATE_INT);
            if ($product_id_to_delete !== false && $product_id_to_delete > 0) {
                if (deleteProduct($pdo, $product_id_to_delete)) {
                    echo "<div class='alert alert-success mt-3' role='alert'>Product deleted successfully!</div>";
                } else {
                    throw new Exception("Failed to delete product.");
                }
            } else {
                throw new Exception("Invalid product ID for deletion.");
            }
        }

        // Handle user deletion (assuming you have a user table and a form for this)
        if (isset($_POST['delete_user_id'])) {
            $user_id_to_delete = filter_var($_POST['delete_user_id'], FILTER_VALIDATE_INT);
            if ($user_id_to_delete !== false && $user_id_to_delete > 0) {
                if (deleteUser($pdo, $user_id_to_delete)) {
                    echo "<div class='alert alert-success mt-3' role='alert'>User deleted successfully!</div>";
                } else {
                    throw new Exception("Failed to delete user.");
                }
            } else {
                throw new Exception("Invalid user ID for deletion.");
            }
        }

        // Handle product upload (this part is moved from the other file)
        if (isset($_POST['name'])) {
            $name = isset($_POST['name']) ? trim($_POST['name']) : null;
            $price = isset($_POST['price']) ? filter_var($_POST['price'], FILTER_VALIDATE_FLOAT) : null;
            $quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : null;
            $category = isset($_POST['category']) ? trim($_POST['category']) : null; // Corrected line!

            if (empty($name)) throw new Exception("Product name is required.");
            if ($price === false || $price <= 0) throw new Exception("Invalid price value.");
            if ($quantity === false || $quantity <= 0) throw new Exception("Invalid quantity.");
            if (empty($category)) throw new Exception("Product category is required.");

            $img_path = null;
            if (!empty($_FILES['image']['name'])) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileInfo = pathinfo(basename($_FILES['image']['name']));
                $fileExtension = strtolower($fileInfo['extension']);

                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new Exception("Invalid file type. Only JPG and PNG files are allowed.");
                }

                $uploadDir = '../backend/uploads/'; // Adjusted upload directory
                if (!file_exists($uploadDir)) {
                    if (!mkdir($uploadDir, 0755, true)) {
                        throw new Exception("Failed to create upload directory.");
                    }
                }

                $img_path = $uploadDir . uniqid() . "." . $fileExtension;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $img_path)) {
                    throw new Exception("Failed to upload image.");
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO product (name, price, quantity, category, img_path)
                VALUES (:name, :price, :quantity, :category, :img_path)
            ");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':img_path', $img_path);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success mt-3' role='alert'>Product uploaded successfully!</div>";
            } else {
                throw new Exception("Failed to save product.");
            }
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger mt-3' role='alert'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

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

<nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-gold">
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

                <?php if (isset($_SESSION['user_id']) ): ?>
                    <a href="../front/profile.php" class="text-white me-3 text-decoration-none">
                        <i class="bi bi-person-circle fs-3"></i> </a>
                    <a href="../backend/logout.php" class="btn btn-outline-gold me-2">Kijelentkezés</a>
                <?php else: ?>
                    <a href="../front/logIn.php" class="btn btn-outline-gold me-2">Bejelentkezés</a>
                    <a href="../front/register.php" class="btn btn-gold">Regisztráció</a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center mb-4">Admin Panel</h1>

    <div class="row">
        <div class="col-md-6">
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
                    <label for="category" class="form-label">Category:</label>
                    <input type="text" name="category" id="category" class="form-control" required>
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

        <div class="col-md-6">
            <h2>Manage Products</h2>
            <?php
            // Display all products with delete buttons
            echo "<ul class='list-group mt-3'>";
            $result = $pdo->query("SELECT * FROM product");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                echo "<div class='d-flex align-items-center'>";
                if ($row['img_path']) {
                    echo "<img src='" . htmlspecialchars(str_replace('../backend/', '', $row['img_path'])) . "' alt='" . htmlspecialchars($row['name']) . "' style='width:50px; height:auto; margin-right: 10px;'>";
                }
                echo "<strong style='margin-right: 10px;'>" . htmlspecialchars($row['name']) . "</strong>";
                echo "€" . number_format($row['price'], 2);
                echo "</div>";
                echo "<form method='post' style='display:inline;'>";
                echo "<input type='hidden' name='delete_product_id' value='" . $row['id'] . "'>";
                echo "<button type='submit' class='btn btn-danger btn-sm'>Delete</button>";
                echo "</form>";
                echo "</li>";
            }
            echo "</ul>";
            ?>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <h2>Manage Users</h2>
            <?php
            // Display all users with delete buttons (assuming you have a 'users' table with 'username' and 'user_id')
            echo "<ul class='list-group mt-3'>";
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
            echo "</ul>";
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>