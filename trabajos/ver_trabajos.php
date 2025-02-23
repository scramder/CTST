<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Trabajos</title>
    <link rel="stylesheet" href="../css/styles.css">
    
    <script>
        function confirmDelete(trabajoId) {
            if (confirm("¿Estás seguro de que deseas eliminar este trabajo?")) {
                window.location.href = "borrar_trabajo.php?id=" + trabajoId;
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <?php
        session_start();

        // Verificar si el usuario tiene el rol de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            echo "Acceso denegado";
            exit;
        }

        echo "Bienvenido, " . $_SESSION['nombre_usuario'];
        ?>
    </div>

    <div class="content">
        <h1>Lista de Trabajos</h1>

        <!-- Filtros -->
        <form method="GET" action="ver_trabajos.php">
            Filtrar por estado: 
            <select name="estado">
                <option value="">Todos</option>
                <option value="Nuevo Ingreso">Nuevo Ingreso</option>
                <option value="En espera">En espera</option>
                <option value="Para presupuestar">Para presupuestar</option>
                <option value="Esperando repuestos">Esperando repuestos</option>
                <option value="Reparando">Reparando</option>
                <option value="Finalizado">Finalizado</option>
                <option value="Entregado">Entregado</option>
                <option value="Anulado">Anulado</option>
                <option value="Cancelado">Cancelado</option>
            </select>
            <input type="submit" value="Filtrar">
        </form>

        <!-- Listado de trabajos -->
        <table class="trabajos">
            <tr>
                <th>ID</th>
                <th>Código de Trabajo</th>
                <th>Estado</th>
                <th>Cliente</th>
                <th>Dispositivo</th>
                <th>Acciones</th>
            </tr>
            <?php
            include '../connect/db_connect.php';

            // Paginación
            $limit = 10; // Número de trabajos por página
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Obtener los trabajos de la base de datos
            $sql = "SELECT t.id, t.codigo_trabajo, t.estado, c.nombres, c.apellido, d.marca, d.modelo
                    FROM trabajos t
                    JOIN dispositivos d ON t.dispositivo_id = d.id
                    JOIN clientes c ON d.cliente_id = c.id";
            if (isset($_GET['estado']) && $_GET['estado'] != '') {
                $estado = $_GET['estado'];
                $sql .= " WHERE t.estado = '$estado'";
            }
            $sql .= " LIMIT $limit OFFSET $offset";
            $result = $conn->query($sql);

            // Contar el número total de trabajos para la paginación
            $sql_count = "SELECT COUNT(*) AS total FROM trabajos";
            $result_count = $conn->query($sql_count);
            $total_trabajos = $result_count->fetch_assoc()['total'];
            $total_pages = ceil($total_trabajos / $limit);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['codigo_trabajo'] . '</td>';
                    echo '<td>' . $row['estado'] . '</td>';
                    echo '<td>' . $row['nombres'] . ' ' . $row['apellido'] . '</td>';
                    echo '<td>' . $row['marca'] . ' ' . $row['modelo'] . '</td>';
                    echo '<td>';
                    echo '<a href="ver_trabajo.php?id=' . $row['id'] . '"><img src="../icons/view.png" alt="Ver"></a>';
                    echo '<a href="modificar_trabajo.php?id=' . $row['id'] . '"><img src="../icons/edit.png" alt="Modificar"></a>';
                    echo '<a href="javascript:void(0);" onclick="confirmDelete(' . $row['id'] . ')"><img src="../icons/delete.png" alt="Borrar"></a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" class="no-trabajos">No hay trabajos</td></tr>';
            }
            ?>
        </table>

        <!-- Paginación -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="ver_trabajos.php?page=' . $i . '">' . $i . '</a> ';
            }
            ?>
        </div>

        <!-- Botones para crear nuevo trabajo y volver al panel de administrador -->
        <div class="actions">
            <a href="nuevo_trabajo.php" class="btn">Crear Nuevo Trabajo</a>
            <a href="../admin/administrador.php" class="btn">Volver al Panel de Administrador</a>
        </div>

        <?php
        $conn->close();
        ?>
    </div>
</body>
</html>