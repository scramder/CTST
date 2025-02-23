<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/trabajos/modificar_trabajos.php
session_start();

// Verificar si el usuario tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'tecnico')) {
    echo "Acceso denegado";
    exit;
}

include '../connect/db_connect.php';

// Obtener el ID del trabajo desde la URL
$trabajo_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Obtener los detalles del trabajo de la base de datos
$sql = "SELECT * FROM trabajos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trabajo_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $trabajo = $result->fetch_assoc();
} else {
    echo "<p>Trabajo no encontrado</p>";
    exit;
}

// Actualizar los datos del trabajo si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo_trabajo = $_POST['codigo_trabajo'];
    $estado = $_POST['estado'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $sql_update = "UPDATE trabajos SET codigo_trabajo = ?, estado = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssi", $codigo_trabajo, $estado, $descripcion, $fecha_inicio, $fecha_fin, $trabajo_id);

    if ($stmt_update->execute()) {
        echo '<script>alert("Trabajo actualizado con éxito"); window.location.href = "ver_trabajos.php";</script>';
    } else {
        echo "<p>Error al actualizar el trabajo: " . $stmt_update->error . "</p>";
    }

    $stmt_update->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Trabajo</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Modificar Trabajo</h1>
    <form method="POST" action="">
        <label for="codigo_trabajo">Código de Trabajo:</label>
        <input type="text" id="codigo_trabajo" name="codigo_trabajo" value="<?php echo $trabajo['codigo_trabajo']; ?>" required>
        <br>
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
            <option value="Nuevo Ingreso" <?php if ($trabajo['estado'] == 'Nuevo Ingreso') echo 'selected'; ?>>Nuevo Ingreso</option>
            <option value="En espera" <?php if ($trabajo['estado'] == 'En espera') echo 'selected'; ?>>En espera</option>
            <option value="Para presupuestar" <?php if ($trabajo['estado'] == 'Para presupuestar') echo 'selected'; ?>>Para presupuestar</option>
            <option value="Esperando repuestos" <?php if ($trabajo['estado'] == 'Esperando repuestos') echo 'selected'; ?>>Esperando repuestos</option>
            <option value="Reparando" <?php if ($trabajo['estado'] == 'Reparando') echo 'selected'; ?>>Reparando</option>
            <option value="Finalizado" <?php if ($trabajo['estado'] == 'Finalizado') echo 'selected'; ?>>Finalizado</option>
            <option value="Entregado" <?php if ($trabajo['estado'] == 'Entregado') echo 'selected'; ?>>Entregado</option>
            <option value="Anulado" <?php if ($trabajo['estado'] == 'Anulado') echo 'selected'; ?>>Anulado</option>
            <option value="Cancelado" <?php if ($trabajo['estado'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
        </select>
        <br>
        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required><?php echo $trabajo['descripcion']; ?></textarea>
        <br>
        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $trabajo['fecha_inicio']; ?>" required>
        <br>
        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $trabajo['fecha_fin']; ?>" required>
        <br>
        <input type="submit" value="Actualizar Trabajo">
    </form>
    <a href="ver_trabajos.php">Volver a la lista de trabajos</a>
</body>
</html>