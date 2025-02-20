<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Dispositivos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function confirmDelete(dispositivoId) {
            if (confirm("¿Estás seguro de que deseas borrar este dispositivo?")) {
                window.location.href = "ver_dispositivos.php?delete_id=" + dispositivoId;
            }
        }

        function showSuccessMessage() {
            alert("Dispositivo borrado exitosamente");
        }
    </script>
</head>
<body>
    <?php
    session_start();

    // Verificar si el usuario tiene el rol de administrador
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
        echo "Acceso denegado";
        exit;
    }

    include '../connect/db_connect.php';

    // Eliminar el dispositivo si se recibió el ID de eliminación
    if (isset($_GET['delete_id'])) {
        $dispositivo_id = $_GET['delete_id'];
        $sql_delete = "DELETE FROM dispositivos WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $dispositivo_id);

        if ($stmt_delete->execute()) {
            echo '<script>showSuccessMessage();</script>';
        } else {
            echo "<p>Error al borrar el dispositivo: " . $stmt_delete->error . "</p>";
        }

        $stmt_delete->close();
    }

    // Paginación
    $limit = 10; // Número de dispositivos por página
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Obtener los dispositivos de la base de datos
    $sql = "SELECT d.id, d.tipo, d.marca, d.modelo, d.serie, d.fecha_ingreso, c.nombres, c.apellido
            FROM dispositivos d
            LEFT JOIN clientes c ON d.cliente_id = c.id
            LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    // Contar el número total de dispositivos para la paginación
    $sql_count = "SELECT COUNT(*) AS total FROM dispositivos";
    $result_count = $conn->query($sql_count);
    $total_dispositivos = $result_count->fetch_assoc()['total'];
    $total_pages = ceil($total_dispositivos / $limit);

    echo "<h1>Administrar Dispositivos</h1>";

    // Listado de dispositivos
    echo '<table>';
    echo '<tr><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Serie</th><th>Fecha de Ingreso</th><th>Cliente</th><th>Acciones</th></tr>';
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['tipo'] . '</td>';
            echo '<td>' . $row['marca'] . '</td>';
            echo '<td>' . $row['modelo'] . '</td>';
            echo '<td>' . $row['serie'] . '</td>';
            echo '<td>' . $row['fecha_ingreso'] . '</td>';
            echo '<td>' . $row['nombres'] . ' ' . $row['apellido'] . '</td>';
            echo '<td>';
            echo '<a href="ver_dispositivo.php?id=' . $row['id'] . '"><img src="../icons/view.png" alt="Ver"></a>';
            echo '<a href="modificar_dispositivo.php?id=' . $row['id'] . '"><img src="../icons/edit.png" alt="Modificar"></a>';
            echo '<a href="javascript:void(0);" onclick="confirmDelete(' . $row['id'] . ')"><img src="../icons/delete.png" alt="Borrar"></a>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No hay dispositivos</td></tr>';
    }
    echo '</table>';

    // Paginación
    echo '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<a href="ver_dispositivos.php?page=' . $i . '">' . $i . '</a> ';
    }
    echo '</div>';

    $conn->close();
    ?>
</body>
</html>