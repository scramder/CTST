<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/trabajos/obtener_nombre_cliente.php
include '../connect/db_connect.php';

$apellido = $_GET['apellido'];

$sql = "SELECT id, nombres, tel_cel FROM clientes WHERE apellido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $apellido);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['id' => $row['id'], 'nombre' => $row['nombres'], 'telefono' => $row['tel_cel']]);
} else {
    echo json_encode(['error' => 'Cliente no encontrado']);
}

$stmt->close();
$conn->close();
?>