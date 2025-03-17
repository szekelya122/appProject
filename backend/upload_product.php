<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "modell/webshop.php"; // Ensure this file properly initializes $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and sanitize input
        $name = isset($_POST['name']) ? trim($_POST['name']) : null;
        $price = isset($_POST['price']) ? filter_var($_POST['price'], FILTER_VALIDATE_FLOAT) : null;
        $quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : null;
        $category_id = isset($_POST['category_id']) ? filter_var($_POST['category_id'], FILTER_VALIDATE_INT) : null;

        // Input validation
        if (empty($name)) {
            throw new Exception("Product name is required.");
        }
        if ($price === false || $price <= 0) {
            throw new Exception("Invalid price value.");
        }
        if ($quantity === false || $quantity <= 0) {
            throw new Exception("Invalid quantity.");
        }
        if ($category_id === false || $category_id <= 0) {
            throw new Exception("Invalid category.");
        }

        // Handle image upload (optional)
        $img_path = null;
        if (!empty($_FILES['image']['name'])) {
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $fileInfo = pathinfo($_FILES['image']['name']);
            $fileExtension = strtolower($fileInfo['extension']);

            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Invalid file type. Only JPG and PNG files are allowed.");
            }

            // Create upload directory if not exists
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                throw new Exception("Failed to create upload directory.");
            }

            // Generate a unique file name
            $img_path = $uploadDir . uniqid() . "." . $fileExtension;

            // Move uploaded file to the target directory
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $img_path)) {
                throw new Exception("Failed to upload image.");
            }
        }

        // Insert into database using prepared statement
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
            http_response_code(200);
            echo json_encode(["message" => "Product uploaded successfully!", "success" => true]);
        } else {
            throw new Exception("Failed to save product. Please try again.");
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["error" => $e->getMessage(), "success" => false]);
    }
}
?>
