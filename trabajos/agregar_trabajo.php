<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Trabajo</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function showSuccessMessage() {
            alert("Trabajo agregado con éxito");
            window.location.href = "ver_trabajos.php";
        }

        function addAnotherDevice(clienteId) {
            alert("Trabajo agregado con éxito. Ahora puede agregar otro dispositivo.");
            window.location.href = "../dispositivos/agregar_dispositivo.php?cliente_id=" + clienteId;
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
    $dispositivo_id = isset($_GET['dispositivo_id']) ? $_GET['dispositivo_id'] : 0;

    // Obtener los detalles del dispositivo y cliente de la base de datos
    $sql_dispositivo = "SELECT d.marca, d.serie, c.id AS cliente_id, c.nombres, c.apellido FROM dispositivos d JOIN clientes c ON d.cliente_id = c.id WHERE d.id = ?";
    $stmt_dispositivo = $conn->prepare($sql_dispositivo);
    $stmt_dispositivo->bind_param("i", $dispositivo_id);
    $stmt_dispositivo->execute();
    $result_dispositivo = $stmt_dispositivo->get_result();

    if ($result_dispositivo->num_rows > 0) {
        $dispositivo = $result_dispositivo->fetch_assoc();
    } else {
        echo "<p>Dispositivo no encontrado</p>";
        exit;
    }

    // Generar el código de trabajo único
    $codigo_trabajo = strtoupper(substr($dispositivo['nombres'], 0, 1) . substr($dispositivo['apellido'], 0, 1) . substr($dispositivo['marca'], 0, 2) . substr($dispositivo['serie'], -2));

    // Obtener la lista de técnicos
    $sql_tecnicos = "SELECT id, nombre_usuario FROM usuarios WHERE rol = 'tecnico'";
    $result_tecnicos = $conn->query($sql_tecnicos);

    // Agregar el trabajo si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $tecnico_asignado = $_POST['tecnico_asignado'];
        $estado = $_POST['estado'];
        $observaciones = $_POST['observaciones'];
        $add_another_device = isset($_POST['add_another_device']) ? true : false;

        $sql_insert = "INSERT INTO trabajos (codigo_trabajo, dispositivo_id, tecnico_asignado, estado, observaciones) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("siiss", $codigo_trabajo, $dispositivo_id, $tecnico_asignado, $estado, $observaciones);

        if ($stmt_insert->execute()) {
            if ($add_another_device) {
                echo '<script>addAnotherDevice(' . $dispositivo['cliente_id'] . ');</script>';
            } else {
                echo '<script>showSuccessMessage();</script>';
            }
        } else {
            echo "<p>Error al agregar el trabajo: " . $stmt_insert->error . "</p>";
        }

        $stmt_insert->close();
    }
    ?>

    <h1>Agregar Trabajo para <?php echo $dispositivo['nombres'] . ' ' . $dispositivo['apellido']; ?> - Dispositivo: <?php echo $dispositivo['marca'] . ' ' . $dispositivo['serie']; ?></h1>
    <form method="POST" action="">
        <label for="tecnico_asignado">Técnico Asignado:</label>
        <select id="tecnico_asignado" name="tecnico_asignado">
            <option value="">Ninguno</option>
            <?php
            if ($result_tecnicos->num_rows > 0) {
                while($tecnico = $result_tecnicos->fetch_assoc()) {
                    echo '<option value="' . $tecnico['id'] . '">' . $tecnico['nombre_usuario'] . '</option>';
                }
            }
            ?>
        </select>
        <br>
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
            <option value="Nuevo Ingreso">Nuevo Ingreso</option>
            <option value="En espera">En espera</option>
            <option value="Para presupuestar">Para presupuestar</option>
            <option value="Esperando repuestos">Esperando repuestos</option>
            <option value="Reparando">Reparando</option>
            <option value="Finalizado">Finalizado</option>
            <option value="Entregado">Entregado</option>
            <option value="Anulado">Anulado</option>
            <option value="Cancelado">Cancelado</option>
        </select>
        <br>
        <label for="observaciones">Observaciones:</label>
        <textarea id="observaciones" name="observaciones"></textarea>
        <br>
        <input type="submit" value="Agregar Trabajo">
        <input type="submit" name="add_another_device" value="Agregar otro dispositivo al cliente">
    </form>
    <a href="../clientes/ver_clientes.php">Volver a la lista de clientes</a>

    <?php
    $stmt_dispositivo->close();
    $conn->close();
    ?>
</body>
</html>