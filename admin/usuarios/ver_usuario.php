<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Usuario</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <?php
    session_start();

    // Verificar si el usuario tiene el rol de administrador
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
        echo "Acceso denegado";
        exit;
    }

    include '../../connect/db_connect.php';

    // Obtener el ID del usuario desde la URL
    $usuario_id = isset($_GET['id']) ? $_GET['id'] : 0;

    // Obtener los detalles del usuario de la base de datos
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        echo "<h1>Detalles del Usuario</h1>";
        echo "<p><strong>Nombre de Usuario:</strong> " . $usuario['nombre_usuario'] . "</p>";
        echo "<p><strong>Rol:</strong> " . $usuario['rol'] . "</p>";
        echo "<p><strong>Nombres:</strong> " . $usuario['nombres'] . "</p>";
        echo "<p><strong>Apellidos:</strong> " . $usuario['apellidos'] . "</p>";
        echo "<p><strong>Tel/Cel:</strong> " . $usuario['tel_cel'] . "</p>";
    } else {
        echo "<p>Usuario no encontrado</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
    <a href="ver_usuarios.php">Volver a la lista de usuarios</a>
</body>
</html>