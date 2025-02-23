<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/admin/clientes/ver_clientes.php
session_start();

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    echo "Acceso denegado";
    exit;
}

include '../../connect/db_connect.php';

// Eliminar el cliente si se recibió el ID de eliminación
if (isset($_GET['delete_id'])) {
    $cliente_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM clientes WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $cliente_id);

    if ($stmt_delete->execute()) {
        echo '<script>showSuccessMessage();</script>';
    } else {
        echo "<p>Error al borrar el cliente: " . $stmt_delete->error . "</p>";
    }

    $stmt_delete->close();
}

// Paginación
$limit = 10; // Número de clientes por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Obtener los clientes de la base de datos
$sql = "SELECT c.id, c.nombres, c.apellido, c.tel_cel, d.tipo AS tipo_dispositivo, d.fecha_ingreso
        FROM clientes c
        LEFT JOIN dispositivos d ON c.id = d.cliente_id
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Contar el número total de clientes para la paginación
$sql_count = "SELECT COUNT(*) AS total FROM clientes";
$result_count = $conn->query($sql_count);
$total_clients = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_clients / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Clientes</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script>
        function confirmDelete(clienteId) {
            if (confirm("¿Estás seguro de que deseas borrar este cliente?")) {
                window.location.href = "ver_clientes.php?delete_id=" + clienteId;
            }
        }

        function showSuccessMessage() {
            alert("Cliente borrado exitosamente");
        }
    </script>
</head>
<body>
    <h1>Administrar Clientes</h1>
    <div class="clientes-detalles">
        <div class="clientes">
            <div class="clientes-header">
                <div class="clientes-col">Nombre</div>
                <div class="clientes-col">Apellido</div>
                <div class="clientes-col">Tel/Cel</div>
                <div class="clientes-col">Dispositivo</div>
                <div class="clientes-col">Fecha de Ingreso</div>
                <div class="clientes-col">Acciones</div>
            </div>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="clientes-row">';
                    echo '<div class="clientes-col">' . $row['nombres'] . '</div>';
                    echo '<div class="clientes-col">' . $row['apellido'] . '</div>';
                    echo '<div class="clientes-col">' . $row['tel_cel'] . '</div>';
                    echo '<div class="clientes-col">' . $row['tipo_dispositivo'] . '</div>';
                    echo '<div class="clientes-col">' . $row['fecha_ingreso'] . '</div>';
                    echo '<div class="clientes-col acciones">';
                    echo '<a href="ver_cliente.php?id=' . $row['id'] . '"><img src="../../icons/view.png" alt="Ver"></a>';
                    echo '<a href="modificar_cliente.php?id=' . $row['id'] . '"><img src="../../icons/edit.png" alt="Modificar"></a>';
                    echo '<a href="javascript:void(0);" onclick="confirmDelete(' . $row['id'] . ')"><img src="../../icons/delete.png" alt="Borrar"></a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="no-clientes">No hay clientes</div>';
            }
            ?>
        </div>

        <!-- Paginación -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="ver_clientes.php?page=' . $i . '">' . $i . '</a> ';
            }
            ?>
        </div>

        <!-- Botón para volver al panel de administrador -->
        <a href="../administrador.php" class="btn">Volver al Panel de Administrador</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>