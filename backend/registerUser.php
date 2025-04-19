<?php

include "modell/webshop.php";

$feedbackMessage = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $passwordConfirm = trim($_POST['passwordConfirm']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

    $errors = [];

    if (empty($username)) {
        $errors[] = "A felhasználónév megadása kötelező!";
    } elseif (strlen($username) < 5) {
        $errors[] = "A felhasználónévnek legalább 5 karakter hosszúnak kell lennie!";
    }

    
    if (empty($password)) {
        $errors[] = "A jelszó megadása kötelező!";
    } elseif (strlen($password) < 8) {
        $errors[] = "A jelszónak legalább 8 karakter hosszúnak kell lennie!";
    }

    if (empty($email)) {
        $errors[] = "Az email cím megadása kötelező!";
    } elseif (!$email) {
        $errors[] = "Nem megfelelő email formátum!";
    }

    
    if ($password !== $passwordConfirm) {
        $errors[] = "A jelszavak nem egyeznek!";
    }

    if ($errors) {
        foreach ($errors as $error) {
            $feedbackMessage .= "<div class='alert alert-success mt-3' role='alert'>$error</div>";
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
                $feedbackMessage = "<div class='alert alert-success mt-3' role='alert'>Ez az email cím már regisztrálva van.</div>";
            } elseif ($usernameExists) {
                $feedbackMessage = "<div class='alert alert-success mt-3' role='alert'> Ez a felhasználónév már foglalt.</div>";
            } else {
               
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':username' => htmlspecialchars($username),
                    ':password' => $hashedPassword,
                    ':email' => $email,
                ]);

             
                $feedbackMessage = "<p style='color: green;'>Sikeres regisztráció! Átirányítás...</p>";
                
                header("Refresh: 2; URL=../front/index.php?register=success");
                exit;
            }
        } catch (PDOException $e) {
            $feedbackMessage = "<div class='alert alert-success mt-3' role='alert'>Hiba történt: " . $e->getMessage() . "</div>";
        }
    }
}
?>
