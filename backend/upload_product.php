<?php
include "modell/webshop.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $price = isset($_POST['price']) ? filter_var($_POST['price'], FILTER_VALIDATE_FLOAT) : null;

    if (empty($name)) {
        die("Product name is required.");
    }
    if ($price === false || $price <= 0) {
        die("Invalid price value.");
    }

    $targetFile = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            die("Invalid file type. Only JPG and PNG files are allowed.");
        }

        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageName = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . uniqid() . "_" . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            die("Failed to upload image.");
        }
    } else {
        die("Error with the uploaded image.");
    }

    $stmt = $conn->prepare("INSERT INTO product (name, price, img_path) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("sds", $name, $price, $targetFile);

    if ($stmt->execute()) {
        echo "Product uploaded successfully!";
    } else {
        error_log("MySQL Error: " . $stmt->error);
        echo "Failed to save product. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>