<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'webshop';

    // Kapcsolat létrehozása
$connection = new mysqli($host,$dbname, $username, $password);
    // PDO hibakezelés beállítása kivételekre
    
 
?>
