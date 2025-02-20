<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Cliente</title>
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
        echo "<h1>Detalles del Cliente</h1>";
        echo "<p><strong>Nombres:</strong> " . $cliente['nombres'] . "</p>";
        echo "<p><strong>Apellido:</strong> " . $cliente['apellido'] . "</p>";
        echo "<p><strong>Tel/Cel:</strong> " . $cliente['tel_cel'] . "</p>";
        echo "<p><strong>Calle:</strong> " . $cliente['calle'] . "</p>";
        echo "<p><strong>Número:</strong> " . $cliente['numero'] . "</p>";
        echo "<p><strong>Barrio:</strong> " . $cliente['barrio'] . "</p>";
        echo "<p><strong>Localidad:</strong> " . $cliente['localidad'] . "</p>";
        echo "<p><strong>Provincia:</strong> " . $cliente['provincia'] . "</p>";
        echo "<p><strong>Indicaciones:</strong> " . $cliente['indicaciones'] . "</p>";
        echo "<p><strong>CP:</strong> " . $cliente['cp'] . "</p>";
        echo "<p><strong>Horario de Contacto:</strong> " . $cliente['horario_contacto'] . "</p>";
        echo "<p><strong>Observaciones:</strong> " . $cliente['observaciones'] . "</p>";
    } else {
        echo "<p>Cliente no encontrado</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
    <a href="ver_clientes.php">Volver a la lista de clientes</a>
</body>
</html>