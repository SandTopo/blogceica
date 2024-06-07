<?php
require 'vendor/autoload.php';
require 'config.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
session_start(); // Iniciar la sesión

// Comprobar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

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

// Procesar el formulario de creación de post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $body = $_POST['body'];
    $image = isset($_POST['image']) ? $_POST['image'] : null;
    $user_id = $_SESSION['user_id'];
    
    try {
        // Insertar el post en la base de datos
        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder
            ->insert('posts')
            ->values([
                'title' => ':title',
                'body' => ':body',
                'image' => ':image',
                'create_date' => 'CURRENT_TIMESTAMP',
                'user_id' => ':user_id'
            ])
            ->setParameter('title', $title)
            ->setParameter('body', $body)
            ->setParameter('image', $image)
            ->setParameter('user_id', $user_id)
            ->executeQuery();

        // Redirigir al usuario a la página de inicio después de crear el post
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        $error = "Error al crear el post: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Crear Nuevo Post</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="createPost.php" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Título:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="body" class="form-label">Cuerpo del Post:</label>
                <textarea class="form-control" id="body" name="body" rows="6" required></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">URL de la Imagen (opcional):</label>
                <input type="text" class="form-control" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary">Publicar</button>
        </form>
    </div>
</body>
</html>