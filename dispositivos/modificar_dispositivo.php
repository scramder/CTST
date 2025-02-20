<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Dispositivo</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function showSuccessMessage() {
            alert("Dispositivo actualizado con éxito");
            window.location.href = "ver_dispositivos.php";
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

    include '../connect/db_connect.php';

    // Obtener el ID del dispositivo desde la URL
    $dispositivo_id = isset($_GET['id']) ? $_GET['id'] : 0;

    // Obtener los detalles del dispositivo de la base de datos
    $sql = "SELECT * FROM dispositivos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dispositivo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $dispositivo = $result->fetch_assoc();
    } else {
        echo "<p>Dispositivo no encontrado</p>";
        exit;
    }

    // Actualizar los datos del dispositivo si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $tipo = $_POST['tipo'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $serie = $_POST['serie'];
        $accesorios = $_POST['accesorios'];
        $falla_presentada = $_POST['falla_presentada'];
        $condiciones = $_POST['condiciones'];
        $imagenes = $_POST['imagenes'];
        $observaciones = $_POST['observaciones'];

        $sql_update = "UPDATE dispositivos SET tipo = ?, marca = ?, modelo = ?, serie = ?, accesorios = ?, falla_presentada = ?, condiciones = ?, imagenes = ?, observaciones = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssssssi", $tipo, $marca, $modelo, $serie, $accesorios, $falla_presentada, $condiciones, $imagenes, $observaciones, $dispositivo_id);

        if ($stmt_update->execute()) {
            echo '<script>showSuccessMessage();</script>';
        } else {
            echo "<p>Error al actualizar el dispositivo: " . $stmt_update->error . "</p>";
        }

        $stmt_update->close();
    }
    ?>

    <h1>Modificar Dispositivo</h1>
    <form method="POST" action="">
        <label for="tipo">Tipo:</label>
        <input type="text" id="tipo" name="tipo" value="<?php echo $dispositivo['tipo']; ?>" required>
        <br>
        <label for="marca">Marca:</label>
        <input type="text" id="marca" name="marca" value="<?php echo $dispositivo['marca']; ?>" required>
        <br>
        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo" value="<?php echo $dispositivo['modelo']; ?>" required>
        <br>
        <label for="serie">Serie:</label>
        <input type="text" id="serie" name="serie" value="<?php echo $dispositivo['serie']; ?>" required>
        <br>
        <label for="accesorios">Accesorios:</label>
        <textarea id="accesorios" name="accesorios"><?php echo $dispositivo['accesorios']; ?></textarea>
        <br>
        <label for="falla_presentada">Falla Presentada:</label>
        <textarea id="falla_presentada" name="falla_presentada"><?php echo $dispositivo['falla_presentada']; ?></textarea>
        <br>
        <label for="condiciones">Condiciones:</label>
        <textarea id="condiciones" name="condiciones"><?php echo $dispositivo['condiciones']; ?></textarea>
        <br>
        <label for="imagenes">Imagenes:</label>
        <textarea id="imagenes" name="imagenes"><?php echo $dispositivo['imagenes']; ?></textarea>
        <br>
        <label for="observaciones">Observaciones:</label>
        <textarea id="observaciones" name="observaciones"><?php echo $dispositivo['observaciones']; ?></textarea>
        <br>
        <input type="submit" value="Actualizar Dispositivo">
    </form>
    <a href="ver_dispositivos.php">Volver a la lista de dispositivos</a>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>