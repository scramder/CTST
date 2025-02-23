<?php
session_start();

// Verificar si el usuario tiene el rol adecuado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    echo "Acceso denegado";
    exit;
}

include '../connect/db_connect.php';

// Obtener los trabajos de la base de datos
$sql = "SELECT t.id, t.codigo_trabajo, t.estado, t.fecha_ingreso, u.nombre_usuario AS tecnico_asignado,
        c.nombres, c.apellido, d.marca, d.modelo
        FROM trabajos t
        LEFT JOIN usuarios u ON t.tecnico_asignado = u.id
        LEFT JOIN dispositivos d ON t.dispositivo_id = d.id
        LEFT JOIN clientes c ON d.cliente_id = c.id";
if (isset($_GET['estado']) && $_GET['estado'] != '') {
    $estado = $_GET['estado'];
    $sql .= " WHERE t.estado = '$estado'";
}
$result = $conn->query($sql);

// Obtener los IDs de los trabajos para la navegación
$trabajos_ids = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $trabajos_ids[] = $row['id'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var trabajosIds = <?php echo json_encode($trabajos_ids); ?>;
            var currentIndex = -1;

            function loadTrabajo(trabajoId) {
                $.ajax({
                    url: "ver_trabajo_overlay.php",
                    method: "GET",
                    data: { id: trabajoId },
                    success: function(data) {
                        $("#overlay-content").html(data);
                        $("#overlay").show();
                    }
                });
            }

            $(".ver-trabajo").on("click", function() {
                var trabajoId = $(this).data("id");
                currentIndex = trabajosIds.indexOf(trabajoId);
                loadTrabajo(trabajoId);
            });

            $("#overlay").on("click", "#overlay-close", function() {
                $("#overlay").hide();
            });

            $("#overlay").on("click", "#overlay-prev", function() {
                if (currentIndex > 0) {
                    currentIndex--;
                    loadTrabajo(trabajosIds[currentIndex]);
                }
            });

            $("#overlay").on("click", "#overlay-next", function() {
                if (currentIndex < trabajosIds.length - 1) {
                    currentIndex++;
                    loadTrabajo(trabajosIds[currentIndex]);
                }
            });

            // Ocultar el overlay al cargar la página
            $("#overlay").hide();
        });
    </script>
    <style>
        /* Estilos para el overlay */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #overlay-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            color: #000;
            position: relative;
        }

        #overlay-close, #overlay-prev, #overlay-next {
            display: inline-block;
            margin: 20px 10px 0;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        #overlay-close:hover, #overlay-prev:hover, #overlay-next:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php
        // Verificar si el nombre de usuario está presente en la sesión
        if (!isset($_SESSION['nombre_usuario'])) {
            echo "Nombre de usuario no encontrado en la sesión";
            exit;
        }

        // Obtener la ruta de la imagen de perfil del usuario activo
        $nombre_usuario = $_SESSION['nombre_usuario'];
        $sql = "SELECT imagen_usuario FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        $foto_perfil = $usuario['imagen_usuario'] ? $usuario['imagen_usuario'] : 'images/default-profile.png';
        $stmt->close();

        echo '<div class="welcome">';
        echo '<div class="profile-pic"><img src="../' . $foto_perfil . '" alt="Profile Picture" id="profileImage"></div>';
        echo 'Bienvenido, ' . $_SESSION['nombre_usuario'];
        echo '</div>';
        echo '<div class="datetime" id="datetime"></div>';
        ?>
        <form method="POST" action="logout.php" style="display: inline;">
            <button type="submit" class="btn">Cerrar Sesión</button>
        </form>
    </div>

    <div class="content">
        <h1>Panel de Administrador</h1>

        <!-- Filtros -->
        <form method="GET" action="administrador.php">
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
            // Obtener los trabajos de la base de datos
            $sql = "SELECT t.id, t.codigo_trabajo, t.estado, t.fecha_ingreso, u.nombre_usuario AS tecnico_asignado,
                    c.nombres, c.apellido, d.marca, d.modelo
                    FROM trabajos t
                    LEFT JOIN usuarios u ON t.tecnico_asignado = u.id
                    LEFT JOIN dispositivos d ON t.dispositivo_id = d.id
                    LEFT JOIN clientes c ON d.cliente_id = c.id";
            if (isset($_GET['estado']) && $_GET['estado'] != '') {
                $estado = $_GET['estado'];
                $sql .= " WHERE t.estado = '$estado'";
            }
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['codigo_trabajo'] . '</td>';
                    echo '<td>' . $row['estado'] . '</td>';
                    echo '<td>' . $row['nombres'] . ' ' . $row['apellido'] . '</td>';
                    echo '<td>' . $row['marca'] . ' ' . $row['modelo'] . '</td>';
                    echo '<td>';
                    echo '<button class="ver-trabajo" data-id="' . $row['id'] . '">Ver</button>';
                    echo '<a href="../trabajos/modificar_trabajo.php?id=' . $row['id'] . '"><img src="../icons/edit.png" alt="Modificar"></a>';
                    echo '<a href="javascript:void(0);" onclick="confirmDelete(' . $row['id'] . ')"><img src="../icons/delete.png" alt="Borrar"></a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" class="no-trabajos">No hay trabajos</td>';
            }
            ?>
        </table>

        <!-- Paginación -->
        <div class="pagination">
            <?php
            // Contar el número total de trabajos para la paginación
            $sql_count = "SELECT COUNT(*) AS total FROM trabajos";
            $result_count = $conn->query($sql_count);
            $total_trabajos = $result_count->fetch_assoc()['total'];
            $total_pages = ceil($total_trabajos / 10); // Número de trabajos por página

            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="administrador.php?page=' . $i . '">' . $i . '</a> ';
            }
            ?>
        </div>

        <?php
        $conn->close();
        ?>
    </div>

    <div class="content">
        <h2>Opciones</h2>
        <a href="clientes/agregar_cliente.php" class="btn">Crear Nuevo Trabajo</a>
        <a href="usuarios/ver_usuarios.php" class="btn">Ver Usuarios</a>
        <a href="clientes/ver_clientes.php" class="btn">Ver Clientes</a>
        <a href="../trabajos/ver_trabajos.php" class="btn">Ver Trabajos</a>
    </div>

    <!-- Overlay -->
    <div id="overlay">
        <div id="overlay-content"></div>

    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const datetimeElement = document.getElementById('datetime');
            datetimeElement.textContent = now.toLocaleString();
        }

        setInterval(updateTime, 1000);
        updateTime(); // Llamar inmediatamente para mostrar la hora sin esperar 1 segundo
    </script>
</body>
</html>