<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'webshop';
try {
    // Kapcsolat létrehozása
    $connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // PDO hibakezelés beállítása kivételekre
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
}
?>
