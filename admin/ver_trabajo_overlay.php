<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/admin/ver_trabajo_overlay.php
session_start();

// Verificar si el usuario tiene el rol adecuado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    echo "Acceso denegado";
    exit;
}

include '../connect/db_connect.php'; // Ajustar la ruta del archivo de conexión

// Obtener el ID del trabajo desde la URL
$trabajo_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Obtener los detalles del trabajo de la base de datos
$sql_trabajo = "SELECT t.codigo_trabajo, t.estado, t.fecha_ingreso, t.tecnico_asignado, t.observaciones AS observaciones_trabajo, 
                c.apellido, c.nombres, c.tel_cel, 
                d.tipo, d.marca, d.modelo, d.serie, d.accesorios, d.falla_presentada, d.condiciones, d.observaciones AS observaciones_dispositivo
                FROM trabajos t
                JOIN dispositivos d ON t.dispositivo_id = d.id
                JOIN clientes c ON d.cliente_id = c.id
                WHERE t.id = ?";
$stmt_trabajo = $conn->prepare($sql_trabajo);
$stmt_trabajo->bind_param("i", $trabajo_id);
$stmt_trabajo->execute();
$result_trabajo = $stmt_trabajo->get_result();

if ($result_trabajo->num_rows > 0) {
    $trabajo = $result_trabajo->fetch_assoc();
} else {
    echo "<p>Trabajo no encontrado</p>";
    exit;
}

$stmt_trabajo->close();
$conn->close();
?>

<div class="trabajo-detalles">
    <div class="trabajo-header">
        <div class="codigo-trabajo">
            <strong>Código de Trabajo:</strong> <?php echo $trabajo['codigo_trabajo']; ?>
        </div>
        <div class="estado-trabajo">
            <strong>Estado:</strong> <?php echo $trabajo['estado']; ?>
        </div>
    </div>
    <h2>Datos del Trabajo</h2>
    <table>
        <tr>
            <td><strong>Fecha de Ingreso:</strong></td>
            <td><?php echo $trabajo['fecha_ingreso']; ?></td>
        </tr>
        <tr>
            <td><strong>Técnico Asignado:</strong></td>
            <td><?php echo $trabajo['tecnico_asignado']; ?></td>
        </tr>
        <tr>
            <td><strong>Observaciones:</strong></td>
            <td><?php echo $trabajo['observaciones_trabajo']; ?></td>
        </tr>
    </table>
    <h2>Datos del Cliente</h2>
    <table>
        <tr>
            <td><strong>Apellido:</strong></td>
            <td><?php echo $trabajo['apellido']; ?></td>
        </tr>
        <tr>
            <td><strong>Nombres:</strong></td>
            <td><?php echo $trabajo['nombres']; ?></td>
        </tr>
        <tr>
            <td><strong>Tel/Cel:</strong></td>
            <td><?php echo $trabajo['tel_cel']; ?></td>
        </tr>
    </table>
    <h2>Datos del Dispositivo</h2>
    <table>
        <tr>
            <td><strong>Tipo:</strong></td>
            <td><?php echo $trabajo['tipo']; ?></td>
        </tr>
        <tr>
            <td><strong>Marca:</strong></td>
            <td><?php echo $trabajo['marca']; ?></td>
        </tr>
        <tr>
            <td><strong>Modelo:</strong></td>
            <td><?php echo $trabajo['modelo']; ?></td>
        </tr>
        <tr>
            <td><strong>Serie:</strong></td>
            <td><?php echo $trabajo['serie']; ?></td>
        </tr>
        <tr>
            <td><strong>Accesorios:</strong></td>
            <td><?php echo $trabajo['accesorios']; ?></td>
        </tr>
        <tr>
            <td><strong>Falla Presentada:</strong></td>
            <td><?php echo $trabajo['falla_presentada']; ?></td>
        </tr>
        <tr>
            <td><strong>Condiciones:</strong></td>
            <td><?php echo $trabajo['condiciones']; ?></td>
        </tr>
        <tr>
            <td><strong>Observaciones:</strong></td>
            <td><?php echo $trabajo['observaciones_dispositivo']; ?></td>
        </tr>
    </table>
    <br>
    <div class="overlay-buttons">
            <button id="overlay-prev" class="btn">Anterior</button>
            <button id="overlay-close" class="btn">Cerrar</button>
            <button id="overlay-next" class="btn">Siguiente</button>
        </div>
</div>