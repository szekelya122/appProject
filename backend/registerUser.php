<?php
// Database configuration
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
    // Collect and sanitize user inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    // Validate inputs
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
        // Display errors
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        try {
            // Check if the email or username already exists
            $checkEmailQuery = "SELECT COUNT(*) FROM users WHERE email = :email";
            $stmt = $pdo->prepare($checkEmailQuery);
            $stmt->execute([':email' => $email]);
            $emailExists = $stmt->fetchColumn() > 0;

            $checkUsernameQuery = "SELECT COUNT(*) FROM users WHERE username = :username";
            $stmt = $pdo->prepare($checkUsernameQuery);
            $stmt->execute([':username' => $username]);
            $usernameExists = $stmt->fetchColumn() > 0;

            if ($emailExists) {
                echo "<p style='color: red;'>A user with this email already exists.</p>";
            } elseif ($usernameExists) {
                echo "<p style='color: red;'>A user with this username already exists.</p>";
            } else {
                // Hash the password before storing it
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert user into the database
                $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':username' => htmlspecialchars($username),
                    ':password' => $hashedPassword,
                    ':email' => $email,
                ]);

               
                header("Location: http://localhost/appProject/front/index.html?register=success");
                echo "<p style='color: green;'>User registered successfully!</p>";
                exit; // Always call exit after redirecting to prevent further script execution
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>
