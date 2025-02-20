<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, sans-serif;
        }
        .header {
            width: 100%;
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        .content {
            width: 80%;
            margin: 20px 0;
        }
        .usuarios {
            width: 100%;
            border-collapse: collapse;
        }
        .usuarios th, .usuarios td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .usuarios th {
            background-color: #f8f9fa;
        }
        .no-usuarios {
            text-align: center;
            font-size: 1.2em;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            font-size: 1em;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .profile-pic {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }
        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    <script>
        function confirmDelete(userId) {
            if (confirm("¿Estás seguro de que deseas borrar este usuario?")) {
                window.location.href = "ver_usuarios.php?delete_id=" + userId;
            }
        }

        function showSuccessMessage() {
            alert("Usuario borrado exitosamente");
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
        <h1>Administrar Usuarios</h1>

        <!-- Botón para agregar un nuevo usuario -->
        <a href="agregar_usuario.php" class="btn">Agregar Usuario</a>
        <!-- Botón para volver al panel del administrador -->
        <a href="../administrador.php" class="btn">Volver al Panel de Administrador</a>

        <!-- Listado de usuarios -->
        <table class="usuarios">
            <tr>
                <th>Foto</th>
                <th>Nombre de Usuario</th>
                <th>Rol</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Tel/Cel</th>
                <th>Acciones</th>
            </tr>
            <?php
            include '../../connect/db_connect.php';

            // Eliminar el usuario si se recibió el ID de eliminación
            if (isset($_GET['delete_id'])) {
                $usuario_id = $_GET['delete_id'];
                $sql_delete = "DELETE FROM usuarios WHERE id = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("i", $usuario_id);

                if ($stmt_delete->execute()) {
                    echo '<script>showSuccessMessage();</script>';
                } else {
                    echo "<p>Error al borrar el usuario: " . $stmt_delete->error . "</p>";
                }

                $stmt_delete->close();
            }

            // Obtener los usuarios de la base de datos
            $sql = "SELECT * FROM usuarios";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td><div class="profile-pic"><img src="../../' . ($row['imagen_usuario'] ? $row['imagen_usuario'] : 'images/default-profile.png') . '" alt="Foto de Perfil"></div></td>';
                    echo '<td>' . $row['nombre_usuario'] . '</td>';
                    echo '<td>' . $row['rol'] . '</td>';
                    echo '<td>' . $row['nombres'] . '</td>';
                    echo '<td>' . $row['apellidos'] . '</td>';
                    echo '<td>' . $row['tel_cel'] . '</td>';
                    echo '<td>';
                    echo '<a href="ver_usuario.php?id=' . $row['id'] . '"><img src="../../icons/view.png" alt="Ver"></a>';
                    echo '<a href="modificar_usuario.php?id=' . $row['id'] . '"><img src="../../icons/edit.png" alt="Modificar"></a>';
                    echo '<a href="javascript:void(0);" onclick="confirmDelete(' . $row['id'] . ')"><img src="../../icons/delete.png" alt="Borrar"></a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7" class="no-usuarios">No hay usuarios</td></tr>';
            }

            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>