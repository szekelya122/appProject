<?php
require_once 'config.php'; 
if ($config) {
    echo "Database connection successful!";
} else {
    echo "Failed to connect to the database.";
}
?>
