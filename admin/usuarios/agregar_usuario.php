<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script>
        function showSuccessMessage() {
            alert("Usuario creado con éxito");
            window.location.href = "ver_usuarios.php";
        }
    </script>
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

    // Agregar el usuario si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre_usuario = $_POST['nombre_usuario'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = $_POST['rol'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $tel_cel = $_POST['tel_cel'];

        $sql_insert = "INSERT INTO usuarios (nombre_usuario, password, rol, nombres, apellidos, tel_cel) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssss", $nombre_usuario, $password, $rol, $nombres, $apellidos, $tel_cel);

        if ($stmt_insert->execute()) {
            echo '<script>showSuccessMessage();</script>';
        } else {
            echo "<p>Error al agregar el usuario: " . $stmt_insert->error . "</p>";
        }

        $stmt_insert->close();
    }
    ?>

    <h1>Agregar Usuario</h1>
    <form method="POST" action="">
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="administrador">Administrador</option>
            <option value="mesa_de_entrada">Mesa de Entrada</option>
            <option value="tecnico">Técnico</option>
        </select>
        <br>
        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" required>
        <br>
        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required>
        <br>
        <label for="tel_cel">Tel/Cel:</label>
        <input type="text" id="tel_cel" name="tel_cel" required>
        <br>
        <input type="submit" value="Agregar Usuario">
    </form>
    <a href="ver_usuarios.php">Volver a la lista de usuarios</a>

    <?php
    $conn->close();
    ?>
</body>
</html>