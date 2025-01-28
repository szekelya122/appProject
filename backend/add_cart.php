<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "root"; // Replace with your MySQL root password
$dbname = "webshop";

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to add items to the cart
    function addToCart($userId, $productId, $quantity) {
        global $conn;

        // Prepare the SQL statement to call the stored procedure
        $stmt = $conn->prepare("CALL AddToCart(:user_id, :product_id, :quantity)");

        // Bind parameters
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

        // Execute the stored procedure
        $stmt->execute();

        echo "Product added to cart successfully!";
    }

    // Example usage
    $userId = 1;         // Replace with the actual user ID
    $productId = 1;      // Replace with the actual product ID
    $quantity = 2;       // Replace with the quantity to add

    // Add the product to the cart
    addToCart($userId, $productId, $quantity);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
