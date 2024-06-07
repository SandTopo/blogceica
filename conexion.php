<?php
require_once("config.php");
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Establecer el modo de error PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa"; 
} catch(PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
}


?>