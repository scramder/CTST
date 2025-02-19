<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../css/styles.css">
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

    echo "Bienvenido, " . $_SESSION['nombre_usuario'];
    echo "<h1>Panel de Administrador</h1>";

    // Filtros
    echo '<form method="GET" action="administrador.php">';
    echo 'Filtrar por estado: <select name="estado">';
    echo '<option value="">Todos</option>';
    echo '<option value="nuevo">Nuevo</option>';
    echo '<option value="en_proceso">En Proceso</option>';
    echo '<option value="completado">Completado</option>';
    echo '</select>';
    echo '<input type="submit" value="Filtrar">';
    echo '</form>';

    // Obtener los trabajos de la base de datos
    $sql = "SELECT * FROM trabajos";
    if (isset($_GET['estado']) && $_GET['estado'] != '') {
        $estado = $_GET['estado'];
        $sql .= " WHERE estado = '$estado'";
    }
    $result = $conn->query($sql);

    // Listado de trabajos
    echo '<div class="trabajos">';
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="trabajo">';
            echo '<div class="trabajo-id">ID: ' . $row['id'] . '</div>';
            echo '<div class="trabajo-descripcion">Descripción: ' . $row['descripcion'] . '</div>';
            echo '<div class="trabajo-estado">Estado: ' . $row['estado'] . '</div>';
            echo '<div class="trabajo-cliente">Cliente: ' . $row['cliente'] . '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="no-trabajos">No hay trabajos</div>';
    }
    echo '</div>';

    // Accesos a otras opciones
    echo '<h2>Opciones</h2>';
    echo '<ul>';
    echo '<li><a href="../ver_usuarios.php">Ver Usuarios</a></li>';
    echo '<li><a href="../ver_clientes.php">Ver Clientes</a></li>';
    echo '</ul>';

    $conn->close();
    ?>
</body>
</html>