<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/trabajos/obtener_dispositivos_cliente.php
include '../connect/db_connect.php';

$apellido = $_GET['apellido'];

$sql = "SELECT d.id, d.tipo, d.marca, d.modelo, d.serie 
        FROM dispositivos d
        JOIN clientes c ON d.cliente_id = c.id
        WHERE c.apellido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $apellido);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $row['tipo'] . '</td>';
    echo '<td>' . $row['marca'] . '</td>';
    echo '<td>' . $row['modelo'] . '</td>';
    echo '<td>' . $row['serie'] . '</td>';
    echo '<td><input type="radio" name="seleccionar_dispositivo" class="seleccionar-dispositivo" data-id="' . $row['id'] . '"></td>';
    echo '</tr>';
}

$stmt->close();
$conn->close();
?>