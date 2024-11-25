<?php
// Database configuration
$host = 'localhost';
$dbname = 'webshop';
$username = 'root';
$password = '';

// Create a connection to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize user inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    // Validate inputs
    if (empty($username) || empty($password) || empty($email)) {
        echo "Username, password, and email are required!";
    } elseif (!$email) {
        echo "Invalid email address!";
    } else {
        try {
            // Hash the password for secure storage
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':email' => $email,
            ]);
            echo "User registered successfully!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
