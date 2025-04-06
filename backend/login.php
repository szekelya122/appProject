<?php
session_start();

// Helyes include útvonal
include "../modell/webshop.php";

// Ha a felhasználó már be van jelentkezve, jelenítsünk meg egy hibaüzenetet
if ($_SESSION['user_id'] != null) {
    echo "<script>alert('Már be vagy jelentkezve!'); window.location.href = '../front/index.php';</script>";
    exit(); // Megállítjuk a további végrehajtást
}   

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // PDO kapcsolat beállítása
        $pdo = new PDO("mysql:host=$host;dbname=webshop", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Felhasználó adatainak lekérdezése, beleértve a szerepkört is
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ha a felhasználó létezik és a jelszó helyes
        if ($user && password_verify($password, $user['password'])) {
            // Bejelentkezés sikeres
            $_SESSION['user_id'] = $user['user_id']; // user_id kell
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 

            // Átirányítás szerepkör alapján
            if ($user['role'] === 'admin') {
                http_response_code(200);
                header('Location: ../front/admin.php');
            } else {
                http_response_code(200);
                header('Location: ../front/index.php?login=success');
            }
            exit();
        } else {
            // Hibás felhasználónév vagy jelszó
            echo "<script>alert('Hibás felhasználónév vagy jelszó!'); window.location.href = '../front/login.php';</script>";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
