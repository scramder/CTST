<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/trabajos/borrar_trabajo.php
session_start();

// Verificar si el usuario tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'tecnico')) {
    echo "Acceso denegado";
    exit;
}

include '../connect/db_connect.php';

// Obtener el ID del trabajo desde la URL
$trabajo_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Eliminar el trabajo de la base de datos
$sql = "DELETE FROM trabajos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trabajo_id);

if ($stmt->execute()) {
    echo '<script>alert("Trabajo eliminado con éxito"); window.location.href = "ver_trabajos.php";</script>';
} else {
    echo '<script>alert("Error al eliminar el trabajo: ' . $stmt->error . '"); window.location.href = "ver_trabajos.php";</script>';
}

$stmt->close();
$conn->close();
?>