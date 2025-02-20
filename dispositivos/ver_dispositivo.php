<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Dispositivo</title>
    <link rel="stylesheet" href="../css/styles.css">
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
        echo "<h1>Detalles del Dispositivo</h1>";
        echo "<p><strong>Tipo:</strong> " . $dispositivo['tipo'] . "</p>";
        echo "<p><strong>Marca:</strong> " . $dispositivo['marca'] . "</p>";
        echo "<p><strong>Modelo:</strong> " . $dispositivo['modelo'] . "</p>";
        echo "<p><strong>Serie:</strong> " . $dispositivo['serie'] . "</p>";
        echo "<p><strong>Accesorios:</strong> " . $dispositivo['accesorios'] . "</p>";
        echo "<p><strong>Falla Presentada:</strong> " . $dispositivo['falla_presentada'] . "</p>";
        echo "<p><strong>Condiciones:</strong> " . $dispositivo['condiciones'] . "</p>";
        echo "<p><strong>Imagenes:</strong> " . $dispositivo['imagenes'] . "</p>";
        echo "<p><strong>Observaciones:</strong> " . $dispositivo['observaciones'] . "</p>";
        echo "<p><strong>Fecha de Ingreso:</strong> " . $dispositivo['fecha_ingreso'] . "</p>";
    } else {
        echo "<p>Dispositivo no encontrado</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
    <a href="ver_dispositivos.php">Volver a la lista de dispositivos</a>
</body>
</html>