<?php
 
include "modell/webshop.php";
session_start();
session_destroy();
header("Location: ../front/index.html");
exit;
?>