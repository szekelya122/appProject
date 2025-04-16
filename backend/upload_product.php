<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "modell/webshop.php";

// Function to delete a product
function deleteProduct($pdo, $product_id) {
    $stmt = $pdo->prepare("DELETE FROM product WHERE id = :id");
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Function to delete a user (assuming you have a user table)
function deleteUser($pdo, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle product deletion
        if (isset($_POST['delete_product_id'])) {
            $product_id_to_delete = filter_var($_POST['delete_product_id'], FILTER_VALIDATE_INT);
            if ($product_id_to_delete !== false && $product_id_to_delete > 0) {
                if (deleteProduct($pdo, $product_id_to_delete)) {
                    echo "<p style='color:green;'>Product deleted successfully!</p>";
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
                    echo "<p style='color:green;'>User deleted successfully!</p>";
                } else {
                    throw new Exception("Failed to delete user.");
                }
            } else {
                throw new Exception("Invalid user ID for deletion.");
            }
        }

        // Handle product upload
        if (isset($_POST['name'])) {
            $name = isset($_POST['name']) ? trim($_POST['name']) : null;
            $price = isset($_POST['price']) ? filter_var($_POST['price'], FILTER_VALIDATE_FLOAT) : null;
            $quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : null;
            $category = isset($_POST['category']) ? filter_var($_POST['category'], FILTER_VALIDATE_INT) : null;

            if (empty($name)) throw new Exception("Product name is required.");
            if ($price === false || $price <= 0) throw new Exception("Invalid price value.");
            if ($quantity === false || $quantity <= 0) throw new Exception("Invalid quantity.");
            if (empty($category)) throw new Exception("Invalid category.");

            $img_path = null;
            if (!empty($_FILES['image']['name'])) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileInfo = pathinfo(basename($_FILES['image']['name']));
                $fileExtension = strtolower($fileInfo['extension']);

                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new Exception("Invalid file type. Only JPG and PNG files are allowed.");
                }

                $uploadDir = 'uploads/';
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
                echo "<p style='color:green;'>Product uploaded successfully!</p>";
            } else {
                throw new Exception("Failed to save product.");
            }
        }

        // Display all products
        echo "<h1>Products</h1><ul>";
        $result = $pdo->query("SELECT * FROM product");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>";
            if ($row['img_path']) {
                echo "<img src='" . htmlspecialchars($row['img_path']) . "' alt='" . htmlspecialchars($row['name']) . "' style='width:100px; height:auto;'><br>";
            }
            echo "<strong>" . htmlspecialchars($row['name']) . "</strong><br>";
            echo "Price: €" . number_format($row['price'], 2) . "<br>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='delete_product_id' value='" . $row['id'] . "'>";
            echo "<button type='submit' style='color:red;'>Delete Product</button>";
            echo "</form>";
            echo "</li>";
        }
        echo "</ul>";

        
        echo "<h1>Users</h1><ul>";
        $result_users = $pdo->query("SELECT * FROM user"); 
        while ($user = $result_users->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>";
            echo "<strong>" . htmlspecialchars($user['username']) . "</strong> (ID: " . $user['id'] . ")<br>";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='delete_user_id' value='" . $user['id'] . "'>";
            echo "<button type='submit' style='color:red;'>Delete User</button>";
            echo "</form>";
            echo "</li>";
        }
        echo "</ul>";

    } catch (Exception $e) {
        http_response_code(400);
        echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

