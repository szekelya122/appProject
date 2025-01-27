<?php
 $host = 'localhost';
 $dbname = 'webshop';
 $username = 'root';
 $password = '';
 $password = 'root';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    

    
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    

    if ($errors) {
        
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        try {
            // Hash the password for secure storage
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => htmlspecialchars($username),
                ':password' => $hashedPassword,
                ':email' => $email,
            ]);

            echo "<p style='color: green;'>User registered successfully!</p>";
        } catch (PDOException $e) {
            // Handle unique constraint violations for email or username
            if ($e->getCode() == 23000) {
                echo "<p style='color: red;'>A user with this email or username already exists.</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            }
        }
    }
}
?>  
