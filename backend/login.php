<?php
session_start();

// MySQL kapcsolódás
$servername = "localhost";
$username = "root";  // Az adatbázis felhasználó neve
$password = "root";      // Az adatbázis jelszava
$dbname = "webshop"; // Az adatbázis neve

$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolódás ellenőrzése
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Felhasználó ellenőrzése az adatbázisból
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Jelszó ellenőrzés (a jelszó hash-elve van az adatbázisban)
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            
            exit();
        } else {
            $error_message = "Hibás felhasználónév vagy jelszó.";
        }
    } else {
        $error_message = "Hibás felhasználónév vagy jelszó.";
    }
}