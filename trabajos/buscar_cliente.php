<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/trabajos/buscar_cliente.php
include '../connect/db_connect.php';

$apellido = $_GET['apellido'];

$sql = "SELECT DISTINCT apellido FROM clientes WHERE apellido LIKE ?";
$stmt = $conn->prepare($sql);
$apellido = "%$apellido%";
$stmt->bind_param("s", $apellido);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<div class="apellido-item">' . $row['apellido'] . '</div>';
}

$stmt->close();
$conn->close();
?>