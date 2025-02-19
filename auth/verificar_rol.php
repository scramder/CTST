<?php
include '../connect/db_connect.php';

session_start();

$nombre_usuario = $_POST['nombre_usuario'];
$password = $_POST['password'];

// Consulta para verificar el usuario y obtener el rol
$sql = "SELECT rol FROM usuarios WHERE nombre_usuario = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nombre_usuario, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['rol'] = $row['rol'];
    $_SESSION['nombre_usuario'] = $nombre_usuario;

    // Redirigir según el rol
    if ($row['rol'] == 'administrador') {
        header("Location: ../admin/administrador.php");
    } elseif ($row['rol'] == 'mesa_de_entrada') {
        header("Location: ../mesa_de_entrada.php");
    } elseif ($row['rol'] == 'tecnico') {
        header("Location: ../tecnico.php");
    } else {
        echo "Rol no reconocido";
    }
} else {
    echo "Usuario o contraseña incorrectos";
}

$stmt->close();
$conn->close();
?>