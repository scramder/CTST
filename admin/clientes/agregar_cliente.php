<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script>
        function showSuccessMessage(clienteId) {
            alert("Cliente agregado con éxito");
            window.location.href = "../../dispositivos/agregar_dispositivo.php?cliente_id=" + clienteId;
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

    // Agregar el cliente si se envió el formulario
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

        $sql_insert = "INSERT INTO clientes (nombres, apellido, tel_cel, calle, numero, barrio, localidad, provincia, indicaciones, cp, horario_contacto, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssssssssss", $nombres, $apellido, $tel_cel, $calle, $numero, $barrio, $localidad, $provincia, $indicaciones, $cp, $horario_contacto, $observaciones);

        if ($stmt_insert->execute()) {
            $cliente_id = $stmt_insert->insert_id;
            echo '<script>showSuccessMessage(' . $cliente_id . ');</script>';
        } else {
            echo "<p>Error al agregar el cliente: " . $stmt_insert->error . "</p>";
        }

        $stmt_insert->close();
    }
    ?>

    <h1>Agregar Cliente</h1>
    <form method="POST" action="">
        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" required>
        <br>
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required>
        <br>
        <label for="tel_cel">Tel/Cel:</label>
        <input type="text" id="tel_cel" name="tel_cel">
        <br>
        <label for="calle">Calle:</label>
        <input type="text" id="calle" name="calle">
        <br>
        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero">
        <br>
        <label for="barrio">Barrio:</label>
        <input type="text" id="barrio" name="barrio">
        <br>
        <label for="localidad">Localidad:</label>
        <input type="text" id="localidad" name="localidad">
        <br>
        <label for="provincia">Provincia:</label>
        <input type="text" id="provincia" name="provincia">
        <br>
        <label for="indicaciones">Indicaciones:</label>
        <textarea id="indicaciones" name="indicaciones"></textarea>
        <br>
        <label for="cp">CP:</label>
        <input type="text" id="cp" name="cp">
        <br>
        <label for="horario_contacto">Horario de Contacto:</label>
        <input type="text" id="horario_contacto" name="horario_contacto">
        <br>
        <label for="observaciones">Observaciones:</label>
        <textarea id="observaciones" name="observaciones"></textarea>
        <br>
        <input type="submit" value="Agregar Cliente">
    </form>
    <a href="../clientes/ver_clientes.php">Volver a la lista de clientes</a>

    <?php
    $conn->close();
    ?>
</body>
</html>