<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script>
        function showSuccessMessage() {
            alert("Usuario creado con éxito");
            window.location.href = "ver_usuarios.php";
        }

        function uploadImage(event) {
            const file = event.target.files[0];
            if (file.size > 2 * 1024 * 1024) {
                alert("El tamaño de la imagen no puede superar los 2MB.");
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const maxSize = 150;
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxSize) {
                            height *= maxSize / width;
                            width = maxSize;
                        }
                    } else {
                        if (height > maxSize) {
                            width *= maxSize / height;
                            height = maxSize;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    document.getElementById('profileImage').src = canvas.toDataURL('image/jpeg');
                }
            }
            reader.readAsDataURL(file);
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

    include '../../connect/db_connect.php';

    // Agregar el usuario si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre_usuario = $_POST['nombre_usuario'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = $_POST['rol'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $tel_cel = $_POST['tel_cel'];
        $imagen_usuario = '';

        // Manejar la subida de la imagen
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
            $foto_perfil = $_FILES['foto_perfil'];
            $imagen_usuario = 'admin/usuarios/img_users/perfil_usuario_' . time() . '.jpg';
            $foto_perfil_path = '../../' . $imagen_usuario;

            // Mover la imagen subida a la carpeta img_users
            if (!move_uploaded_file($foto_perfil['tmp_name'], $foto_perfil_path)) {
                echo "<p>Error al subir la imagen.</p>";
                $imagen_usuario = '';
            }
        }

        $sql_insert = "INSERT INTO usuarios (nombre_usuario, password, rol, nombres, apellidos, tel_cel, imagen_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssssss", $nombre_usuario, $password, $rol, $nombres, $apellidos, $tel_cel, $imagen_usuario);

        if ($stmt_insert->execute()) {
            echo '<script>showSuccessMessage();</script>';
        } else {
            echo "<p>Error al agregar el usuario: " . $stmt_insert->error . "</p>";
        }

        $stmt_insert->close();
    }
    ?>

    <h1>Agregar Usuario</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="administrador">Administrador</option>
            <option value="mesa_de_entrada">Mesa de Entrada</option>
            <option value="tecnico">Técnico</option>
        </select>
        <br>
        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" required>
        <br>
        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required>
        <br>
        <label for="tel_cel">Tel/Cel:</label>
        <input type="text" id="tel_cel" name="tel_cel" required>
        <br>
        <label for="foto_perfil">Foto de Perfil:</label>
        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" onchange="uploadImage(event)">
        <br>
        <img id="profileImage" src="../../images/default-profile.png" alt="Profile Picture" style="width: 150px; height: 150px;">
        <br>
        <input type="submit" value="Agregar Usuario">
    </form>
    <a href="ver_usuarios.php">Volver a la lista de usuarios</a>

    <?php
    $conn->close();
    ?>
</body>
</html>