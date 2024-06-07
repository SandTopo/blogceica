<?php
require 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

$config = new \Doctrine\DBAL\Configuration();

// Configuración de conexión a la base de datos
$connectionParams = [
    'dbname' => 'blog',
    'user' => 'root',
    'password' => '1234',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

$conn = DriverManager::getConnection($connectionParams, $config);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encriptar la contraseña
    $image = '';

    // Procesar la imagen si se ha subido
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
    }

    // Insertar datos en la tabla users
    try {
        $conn->insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'image' => $image
        ]);

        echo "Registro exitoso!";
    } catch (Exception $e) {
        echo "Error al registrar el usuario: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <form action="register.php" method="POST" enctype="multipart/form-data">
        <label for="name">Nombre:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="email">Correo Electrónico:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <label for="image">Imagen (opcional):</label><br>
        <input type="file" id="image" name="image"><br><br>
        
        <input type="submit" value="Registrar">
    </form>
</body>
</html>