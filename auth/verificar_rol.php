<?php
session_start();
include '../connect/db_connect.php';

$nombre_usuario = $_POST['nombre_usuario'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    if (password_verify($password, $usuario['password'])) {
        $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
        $_SESSION['rol'] = $usuario['rol'];
        header("Location: ../admin/administrador.php");
        exit;
    } else {
        header("Location: ../index.html?error=1");
        exit;
    }
} else {
    header("Location: ../index.html?error=1");
    exit;
}

$stmt->close();
$conn->close();
?>