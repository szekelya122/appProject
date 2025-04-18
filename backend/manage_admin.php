<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "modell/webshop.php";


$categories = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If there's an error, show a message
    $feedbackMessage = "<div class='alert alert-danger mt-3' role='alert'>Error loading categories: " . htmlspecialchars($e->getMessage()) . "</div>";
}
function deleteProduct($pdo, $product_id) {
    $stmt = $pdo->prepare("DELETE FROM product WHERE id = :id");
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Function to delete a user
function deleteUser($pdo, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete_product_id'])) {
            $product_id_to_delete = filter_var($_POST['delete_product_id'], FILTER_VALIDATE_INT);
            if ($product_id_to_delete !== false && $product_id_to_delete > 0) {
                if (deleteProduct($pdo, $product_id_to_delete)) {
                    $feedbackMessage = "<div class='alert alert-success mt-3' role='alert'>Product deleted successfully!</div>";
                } else {
                    throw new Exception("Failed to delete product.");
                }
            } else {
                throw new Exception("Invalid product ID for deletion.");
            }
        }

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
            
           


        if (isset($_POST['name'])) {
            $name = trim($_POST['name']);
            $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
            $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
            $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
            if (empty($name)) throw new Exception("Product name is required.");
            if ($price === false || $price <= 0) throw new Exception("Invalid price value.");
            if ($quantity === false || $quantity <= 0) throw new Exception("Invalid quantity.");
            if ($category_id === false || $category_id <= 0) throw new Exception("Product category is required.");

            $img_path = null;
            if (!empty($_FILES['image']['name'])) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileInfo = pathinfo(basename($_FILES['image']['name']));
                $fileExtension = strtolower($fileInfo['extension']);

                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new Exception("Invalid file type. Only JPG and PNG files are allowed.");
                }

                $uploadDir = '../backend/uploads/';
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
                INSERT INTO product (name, price, quantity, category_id, img_path)
                VALUES (:name, :price, :quantity, :category_id, :img_path)
            ");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':img_path', $img_path);

            if ($stmt->execute()) {
                $feedbackMessage = "<div class='alert alert-success mt-3' role='alert'>Product uploaded successfully!</div>";

            } else {
                throw new Exception("Failed to save product.");
            }
        }
    } catch (Exception $e) {
        $feedbackMessage = "<div class='alert alert-danger mt-3' role='alert'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>