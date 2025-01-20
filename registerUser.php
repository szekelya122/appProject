<?php
<<<<<<< Updated upstream
// Database configuration
=======
>>>>>>> Stashed changes
$host = 'localhost';
$dbname = 'webshop';
$username = 'root';
$password = 'root';

// Create a connection to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
<<<<<<< Updated upstream
    // Collect and sanitize user inputs
=======
>>>>>>> Stashed changes
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

<<<<<<< Updated upstream
    // Validate inputs
=======
>>>>>>> Stashed changes
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!$email) {
        $errors[] = "Invalid email address.";
    }

    if ($errors) {
<<<<<<< Updated upstream
        // Display errors
=======
>>>>>>> Stashed changes
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        try {
            // Check if username or email already exists
            $sql = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email
            ]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                echo "<p style='color: red;'>A user with this username or email already exists.</p>";
            } else {
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
                header("Location: http://localhost/appProject/login.html");
                exit();
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>
<<<<<<< Updated upstream
=======
    
>>>>>>> Stashed changes
