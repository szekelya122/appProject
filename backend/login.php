<?php
session_start();


include "../modell/webshop.php";

if ($_SESSION['user_id'] != null) {
    echo "<script>alert('Már be vagy jelentkezve!'); window.location.href = '../front/index.php';</script>";
    exit(); 
}   

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        
        $pdo = new PDO("mysql:host=$host;dbname=webshop", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($user && password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['user_id']; // 
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 

            
            if ($user['role'] === 'admin') {
                http_response_code(200);
                header('Location: ../front/admin.php');
            } else {
                http_response_code(200);
                header('Location: ../front/index.php?login=success');
            }
            exit();
        } else {
            
            echo "<script>alert('Hibás felhasználónév vagy jelszó!'); window.location.href = '../front/login.php';</script>";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
