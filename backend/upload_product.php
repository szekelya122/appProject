<?php
// Database credentials
$host = 'localhost';
$dbname = 'webshop';
$username = 'root';
$password = 'root';

// Display errors for debugging during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create database connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Set character set to handle special characters (e.g., emojis)
    $conn->set_charset("utf8mb4");

    // Validate and sanitize input data
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $price = isset($_POST['price']) ? filter_var($_POST['price'], FILTER_VALIDATE_FLOAT) : null;

    if (empty($name)) {
        die("Product name is required.");
    }
    if ($price === false || $price <= 0) {
        die("Invalid price value.");
    }

    // Handle file upload
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

    // Insert product into the database
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

    // Fetch and display all products
    $result = $conn->query("SELECT * FROM product");
    if ($result->num_rows > 0) {
        echo "<h1>Products</h1><ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>";
            echo "<img src='" . htmlspecialchars($row['img_path']) . "' alt='" . htmlspecialchars($row['name']) . "' style='width:150px; height:auto;'><br>";
            echo "<strong>" . htmlspecialchars($row['name']) . "</strong><br>";
            echo "Price: €" . number_format($row['price'], 2) . "<br>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "No products found.";
    }

    $conn->close();
}
?>
