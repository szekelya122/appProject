<?php

include "modell/webshop.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    
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
      
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        try {
           
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
                
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                
                $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':username' => htmlspecialchars($username),
                    ':password' => $hashedPassword,
                    ':email' => $email,
                ]);

               
                header("Location: ../front/index.html?register=success");
                
                exit; 
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>
