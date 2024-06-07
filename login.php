<?php
require 'vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
session_start(); // Iniciar la sesión

// Configuración de conexión a la base de datos
$config = new \Doctrine\DBAL\Configuration();
$connectionParams = [
   'dbname' => 'blog',
    'user' => 'root',
    'password' => '1234',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

$conn = DriverManager::getConnection($connectionParams, $config);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Buscar el usuario por email
        $queryBuilder = $conn->createQueryBuilder();
        $user = $queryBuilder
            ->select('id', 'name', 'password')
            ->from('users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery()
            ->fetchAssociative();

        if ($user) {
            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Guardar información del usuario en la sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                // Redirigir al usuario a la página de inicio
                header('Location: index.php');
                exit();
            } else {
                // Contraseña incorrecta
                $error = "La contraseña es incorrecta.";
            }
        } else {
            // Usuario no encontrado
            $error = "El correo electrónico no está registrado.";
        }
    } catch (Exception $e) {
        $error = "Error al iniciar sesión: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>