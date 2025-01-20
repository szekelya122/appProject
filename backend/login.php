<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'webshop';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if (!empty($user) && !empty($pass)) {
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $user);
        $stmt->execute();

        $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($userRecord && password_verify($pass, $userRecord['password'])) {
            // Store session variables
            $_SESSION['user_id'] = $userRecord['id'];
            $_SESSION['username'] = $userRecord['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>