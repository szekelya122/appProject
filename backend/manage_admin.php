<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "modell/webshop.php";

$categories = [];
$feedbackMessage = ''; 

try {
    $stmt = $pdo->query("SELECT id, name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $feedbackMessage = "<div class='alert alert-danger mt-3' role='alert'>Error loading categories: " . htmlspecialchars($e->getMessage()) . "</div>";
}


function deleteProduct($pdo, $product_id) {
    $stmt = $pdo->prepare("DELETE FROM product WHERE id = :id");
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    return $stmt->execute();
}


function deleteUser($pdo, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}


function validateInput($data, $type) {
    switch ($type) {
        case 'string':
            return trim($data);
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT);
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT);
        default:
            return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Deleting product
        if (isset($_POST['delete_product_id'])) {
            $product_id_to_delete = validateInput($_POST['delete_product_id'], 'int');
            if ($product_id_to_delete && deleteProduct($pdo, $product_id_to_delete)) {
                $feedbackMessage = "<div class='alert alert-success mt-3' role='alert'>Product deleted successfully!</div>";
            } else {
                throw new Exception("Failed to delete product.");
            }
        }

        // Deleting user
        if (isset($_POST['delete_user_id'])) {
            $user_id_to_delete = validateInput($_POST['delete_user_id'], 'int');
            if ($user_id_to_delete && deleteUser($pdo, $user_id_to_delete)) {
                $feedbackMessage = "<div class='alert alert-success mt-3' role='alert'>User deleted successfully!</div>";
            } else {
                throw new Exception("Failed to delete user.");
            }
        }

        
        if (isset($_POST['name'])) {
            $name = validateInput($_POST['name'], 'string');
            $price = validateInput($_POST['price'], 'float');
            $quantity = validateInput($_POST['quantity'], 'int');
            $category_id = validateInput($_POST['category_id'], 'int');

            if (empty($name)) throw new Exception("Product name is required.");
            if ($price === false || $price <= 0) throw new Exception("Invalid price value.");
            if ($quantity === false || $quantity <= 0) throw new Exception("Invalid quantity.");
            if ($category_id === false || $category_id <= 0) throw new Exception("Product category is required.");

            $img_path = null;

            // File upload handling
            if (!empty($_FILES['image']['name'])) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileInfo = pathinfo(basename($_FILES['image']['name']));
                $fileExtension = strtolower($fileInfo['extension']);

                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new Exception("Invalid file type. Only JPG and PNG files are allowed.");
                }

                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    throw new Exception("File is too large. Maximum size is 5MB.");
                }

                
                $uploadDir = '../backend/uploads/';
                if (!file_exists($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                    throw new Exception("Failed to create upload directory.");
                }

                $img_path = $uploadDir . uniqid() . "." . $fileExtension;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $img_path)) {
                    throw new Exception("Failed to upload image.");
                }
            }

            // Insert product into the database
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

        // Update product quantity
        if (isset($_POST['update_product_id']) && isset($_POST['new_quantity'])) {
            $product_id_to_update = validateInput($_POST['update_product_id'], 'int');
            $new_quantity = validateInput($_POST['new_quantity'], 'int');

            if ($product_id_to_update && $new_quantity >= 0) {
                $stmt = $pdo->prepare("UPDATE product SET quantity = :quantity WHERE id = :id");
                $stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
                $stmt->bindParam(':id', $product_id_to_update, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $feedbackMessage = "<div class='alert alert-success mt-3' role='alert'>Product quantity updated successfully!</div>";
                } else {
                    throw new Exception("Failed to update product quantity.");
                }
            } else {
                throw new Exception("Invalid quantity value.");
            }
        }

    } catch (Exception $e) {
        $feedbackMessage = "<div class='alert alert-danger mt-3' role='alert'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>
