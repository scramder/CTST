<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/cliente_dispositivos.php
session_start();

// Verificar si el usuario tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'tecnico' && $_SESSION['rol'] != 'mesa_de_entrada')) {
    echo "Acceso denegado";
    exit;
}

include '../connect/db_connect.php';

// Obtener la lista de clientes y sus dispositivos
$sql = "SELECT c.nombres, c.apellido, d.marca, d.modelo, d.serie
        FROM clientes c
        JOIN dispositivos d ON c.id = d.cliente_id
        ORDER BY c.apellido, c.nombres";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes y Dispositivos</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Clientes y sus Dispositivos</h1>
    <table class="clientes-dispositivos">
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Marca del Dispositivo</th>
            <th>Modelo del Dispositivo</th>
            <th>Serie del Dispositivo</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['nombres'] . '</td>';
                echo '<td>' . $row['apellido'] . '</td>';
                echo '<td>' . $row['marca'] . '</td>';
                echo '<td>' . $row['modelo'] . '</td>';
                echo '<td>' . $row['serie'] . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5" class="no-data">No hay datos disponibles</td></tr>';
        }
        ?>
    </table>
    <a href="../admin/administrador.php" class="btn">Volver al Panel de Administrador</a>
</body>
</html>

<?php
$conn->close();
?>