<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Dispositivo</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function showSuccessMessage(dispositivoId) {
            alert("Dispositivo agregado con éxito");
            window.location.href = "../trabajos/agregar_trabajo.php?dispositivo_id=" + dispositivoId;
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

    // Obtener el ID del cliente desde la URL
    $cliente_id = isset($_GET['cliente_id']) ? $_GET['cliente_id'] : 0;

    // Obtener los detalles del cliente de la base de datos
    $sql_cliente = "SELECT nombres, apellido FROM clientes WHERE id = ?";
    $stmt_cliente = $conn->prepare($sql_cliente);
    $stmt_cliente->bind_param("i", $cliente_id);
    $stmt_cliente->execute();
    $result_cliente = $stmt_cliente->get_result();

    if ($result_cliente->num_rows > 0) {
        $cliente = $result_cliente->fetch_assoc();
    } else {
        echo "<p>Cliente no encontrado</p>";
        exit;
    }

    // Agregar el dispositivo si se envió el formulario
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

        $sql_insert = "INSERT INTO dispositivos (cliente_id, tipo, marca, modelo, serie, accesorios, falla_presentada, condiciones, imagenes, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isssssssss", $cliente_id, $tipo, $marca, $modelo, $serie, $accesorios, $falla_presentada, $condiciones, $imagenes, $observaciones);

        if ($stmt_insert->execute()) {
            $dispositivo_id = $stmt_insert->insert_id;
            echo '<script>showSuccessMessage(' . $dispositivo_id . ');</script>';
        } else {
            echo "<p>Error al agregar el dispositivo: " . $stmt_insert->error . "</p>";
        }

        $stmt_insert->close();
    }
    ?>

    <h1>Agregar Dispositivo para <?php echo $cliente['nombres'] . ' ' . $cliente['apellido']; ?></h1>
    <form method="POST" action="">
        <label for="tipo">Tipo:</label>
        <input type="text" id="tipo" name="tipo" required>
        <br>
        <label for="marca">Marca:</label>
        <input type="text" id="marca" name="marca" required>
        <br>
        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo" required>
        <br>
        <label for="serie">Serie:</label>
        <input type="text" id="serie" name="serie" required>
        <br>
        <label for="accesorios">Accesorios:</label>
        <textarea id="accesorios" name="accesorios"></textarea>
        <br>
        <label for="falla_presentada">Falla Presentada:</label>
        <textarea id="falla_presentada" name="falla_presentada"></textarea>
        <br>
        <label for="condiciones">Condiciones:</label>
        <textarea id="condiciones" name="condiciones"></textarea>
        <br>
        <label for="imagenes">Imagenes:</label>
        <textarea id="imagenes" name="imagenes"></textarea>
        <br>
        <label for="observaciones">Observaciones:</label>
        <textarea id="observaciones" name="observaciones"></textarea>
        <br>
        <input type="submit" value="Agregar Dispositivo">
    </form>
    <a href="../admin/clientes/ver_clientes.php">Volver a la lista de clientes</a>

    <?php
    $stmt_cliente->close();
    $conn->close();
    ?>
</body>
</html>