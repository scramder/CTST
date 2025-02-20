<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script>
        function showSuccessMessage() {
            alert("Usuario actualizado con éxito");
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
    } else {
        echo "<p>Usuario no encontrado</p>";
        exit;
    }

    // Actualizar los datos del usuario si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre_usuario = $_POST['nombre_usuario'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = $_POST['rol'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $tel_cel = $_POST['tel_cel'];

        $sql_update = "UPDATE usuarios SET nombre_usuario = ?, password = ?, rol = ?, nombres = ?, apellidos = ?, tel_cel = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssi", $nombre_usuario, $password, $rol, $nombres, $apellidos, $tel_cel, $usuario_id);

        if ($stmt_update->execute()) {
            echo '<script>showSuccessMessage();</script>';
        } else {
            echo "<p>Error al actualizar el usuario: " . $stmt_update->error . "</p>";
        }

        $stmt_update->close();
    }
    ?>

    <h1>Modificar Usuario</h1>
    <form method="POST" action="">
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo $usuario['nombre_usuario']; ?>" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="administrador" <?php if ($usuario['rol'] == 'administrador') echo 'selected'; ?>>Administrador</option>
            <option value="mesa_de_entrada" <?php if ($usuario['rol'] == 'mesa_de_entrada') echo 'selected'; ?>>Mesa de Entrada</option>
            <option value="tecnico" <?php if ($usuario['rol'] == 'tecnico') echo 'selected'; ?>>Técnico</option>
        </select>
        <br>
        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" value="<?php echo $usuario['nombres']; ?>" required>
        <br>
        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" value="<?php echo $usuario['apellidos']; ?>" required>
        <br>
        <label for="tel_cel">Tel/Cel:</label>
        <input type="text" id="tel_cel" name="tel_cel" value="<?php echo $usuario['tel_cel']; ?>" required>
        <br>
        <input type="submit" value="Actualizar Usuario">
    </form>
    <a href="ver_usuarios.php">Volver a la lista de usuarios</a>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>