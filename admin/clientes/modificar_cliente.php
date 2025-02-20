<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Cliente</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script>
        function showSuccessMessage() {
            alert("Cliente actualizado con éxito");
            window.location.href = "ver_clientes.php";
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

    // Obtener el ID del cliente desde la URL
    $cliente_id = isset($_GET['id']) ? $_GET['id'] : 0;

    // Obtener los detalles del cliente de la base de datos
    $sql = "SELECT * FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
    } else {
        echo "<p>Cliente no encontrado</p>";
        exit;
    }

    // Actualizar los datos del cliente si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombres = $_POST['nombres'];
        $apellido = $_POST['apellido'];
        $tel_cel = $_POST['tel_cel'];
        $calle = $_POST['calle'];
        $numero = $_POST['numero'];
        $barrio = $_POST['barrio'];
        $localidad = $_POST['localidad'];
        $provincia = $_POST['provincia'];
        $indicaciones = $_POST['indicaciones'];
        $cp = $_POST['cp'];
        $horario_contacto = $_POST['horario_contacto'];
        $observaciones = $_POST['observaciones'];

        $sql_update = "UPDATE clientes SET nombres = ?, apellido = ?, tel_cel = ?, calle = ?, numero = ?, barrio = ?, localidad = ?, provincia = ?, indicaciones = ?, cp = ?, horario_contacto = ?, observaciones = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssssssssi", $nombres, $apellido, $tel_cel, $calle, $numero, $barrio, $localidad, $provincia, $indicaciones, $cp, $horario_contacto, $observaciones, $cliente_id);

        if ($stmt_update->execute()) {
            echo '<script>showSuccessMessage();</script>';
        } else {
            echo "<p>Error al actualizar el cliente: " . $stmt_update->error . "</p>";
        }

        $stmt_update->close();
    }
    ?>

    <h1>Modificar Cliente</h1>
    <form method="POST" action="">
        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" value="<?php echo $cliente['nombres']; ?>" required>
        <br>
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo $cliente['apellido']; ?>" required>
        <br>
        <label for="tel_cel">Tel/Cel:</label>
        <input type="text" id="tel_cel" name="tel_cel" value="<?php echo $cliente['tel_cel']; ?>">
        <br>
        <label for="calle">Calle:</label>
        <input type="text" id="calle" name="calle" value="<?php echo $cliente['calle']; ?>">
        <br>
        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero" value="<?php echo $cliente['numero']; ?>">
        <br>
        <label for="barrio">Barrio:</label>
        <input type="text" id="barrio" name="barrio" value="<?php echo $cliente['barrio']; ?>">
        <br>
        <label for="localidad">Localidad:</label>
        <input type="text" id="localidad" name="localidad" value="<?php echo $cliente['localidad']; ?>">
        <br>
        <label for="provincia">Provincia:</label>
        <input type="text" id="provincia" name="provincia" value="<?php echo $cliente['provincia']; ?>">
        <br>
        <label for="indicaciones">Indicaciones:</label>
        <textarea id="indicaciones" name="indicaciones"><?php echo $cliente['indicaciones']; ?></textarea>
        <br>
        <label for="cp">CP:</label>
        <input type="text" id="cp" name="cp" value="<?php echo $cliente['cp']; ?>">
        <br>
        <label for="horario_contacto">Horario de Contacto:</label>
        <input type="text" id="horario_contacto" name="horario_contacto" value="<?php echo $cliente['horario_contacto']; ?>">
        <br>
        <label for="observaciones">Observaciones:</label>
        <textarea id="observaciones" name="observaciones"><?php echo $cliente['observaciones']; ?></textarea>
        <br>
        <input type="submit" value="Actualizar Cliente">
    </form>
    <a href="ver_clientes.php">Volver a la lista de clientes</a>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>