<?php
// filepath: /c:/xampp/htdocs/lcdc/CTST/trabajos/nuevo_trabajo.php
session_start();

// Verificar si el usuario tiene el rol adecuado
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'tecnico')) {
    echo "Acceso denegado";
    exit;
}

include '../connect/db_connect.php';

// Crear el trabajo si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $dispositivo_id = $_POST['dispositivo_id'];
    $tecnico_asignado = $_POST['tecnico_asignado'];
    $estado = $_POST['estado'];
    $observaciones = $_POST['observaciones'];

    // Obtener detalles del dispositivo y cliente para generar el código de trabajo
    $sql_dispositivo = "SELECT d.marca, d.serie, c.nombres, c.apellido FROM dispositivos d JOIN clientes c ON d.cliente_id = c.id WHERE d.id = ?";
    $stmt_dispositivo = $conn->prepare($sql_dispositivo);
    $stmt_dispositivo->bind_param("i", $dispositivo_id);
    $stmt_dispositivo->execute();
    $result_dispositivo = $stmt_dispositivo->get_result();

    if ($result_dispositivo->num_rows > 0) {
        $dispositivo = $result_dispositivo->fetch_assoc();
    } else {
        echo "<p>Dispositivo no encontrado</p>";
        exit;
    }

    // Generar el código de trabajo único
    $codigo_trabajo = strtoupper(substr($dispositivo['nombres'], 0, 1) . substr($dispositivo['apellido'], 0, 1) . substr($dispositivo['marca'], 0, 2) . substr($dispositivo['serie'], -2));

    $sql_insert = "INSERT INTO trabajos (codigo_trabajo, dispositivo_id, tecnico_asignado, estado, observaciones) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("siiss", $codigo_trabajo, $dispositivo_id, $tecnico_asignado, $estado, $observaciones);

    if ($stmt_insert->execute()) {
        echo '<script>alert("Trabajo creado con éxito"); window.location.href = "ver_trabajos.php";</script>';
    } else {
        echo "<p>Error al crear el trabajo: " . $stmt_insert->error . "</p>";
    }

    $stmt_insert->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Trabajo</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Autocompletar apellido del cliente
            $("#buscar_apellido").on("input", function() {
                var apellido = $(this).val();
                if (apellido.length >= 1) {
                    $.ajax({
                        url: "buscar_cliente.php",
                        method: "GET",
                        data: { apellido: apellido },
                        success: function(data) {
                            $("#lista_apellidos").html(data);
                        }
                    });
                } else {
                    $("#lista_apellidos").html("");
                }
            });

            // Seleccionar apellido del cliente
            $(document).on("click", ".apellido-item", function() {
                var apellido = $(this).text();
                $("#buscar_apellido").val(apellido);
                $("#lista_apellidos").html("");

                // Obtener nombre y teléfono del cliente
                $.ajax({
                    url: "obtener_nombre_cliente.php",
                    method: "GET",
                    data: { apellido: apellido },
                    success: function(data) {
                        console.log(data); // Añadir esta línea para depuración
                        var cliente = JSON.parse(data);
                        if (cliente.error) {
                            alert(cliente.error);
                        } else {
                            $("#apellido_cliente").val(apellido);
                            $("#nombre_cliente").val(cliente.nombre);
                            $("#telefono_cliente").val(cliente.telefono);
                            $("#cliente_id").val(cliente.id);

                            // Mostrar datos del cliente en una tabla
                            var clienteHtml = '<tr><td>' + cliente.nombre + '</td><td>' + apellido + '</td><td>' + cliente.telefono + '</td></tr>';
                            $("#tabla_cliente tbody").html(clienteHtml);
                        }
                    }
                });

                // Obtener dispositivos del cliente
                $.ajax({
                    url: "obtener_dispositivos_cliente.php",
                    method: "GET",
                    data: { apellido: apellido },
                    success: function(data) {
                        $("#tabla_dispositivos tbody").html(data);
                    }
                });
            });

            // Seleccionar dispositivo
            $(document).on("click", ".seleccionar-dispositivo", function() {
                var dispositivoId = $(this).data("id");
                $("#dispositivo_id").val(dispositivoId);
                $(".seleccionar-dispositivo").removeClass("selected");
                $(this).addClass("selected");
            });
        });
    </script>
</head>
<body>
    <h1>Nuevo Trabajo</h1>
    <form method="POST" action="">
        <label for="buscar_apellido">Buscar por Apellido:</label>
        <input type="text" id="buscar_apellido" name="buscar_apellido" required>
        <div id="lista_apellidos"></div>
        <br>
        <h2>Datos del Cliente</h2>
        <table id="tabla_cliente">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellido</th>
                    <th>Tel/Cel</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos del cliente se cargarán aquí -->
            </tbody>
        </table>
        <br>
        <h2>Dispositivos Relacionados</h2>
        <table id="tabla_dispositivos">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Serie</th>
                    <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dispositivos se cargarán aquí -->
            </tbody>
        </table>
        <input type="hidden" id="dispositivo_id" name="dispositivo_id">
        <br>
        <label for="tecnico_asignado">Técnico Asignado:</label>
        <select id="tecnico_asignado" name="tecnico_asignado">
            <option value="">Ninguno</option>
            <?php
            include '../connect/db_connect.php';
            $sql_tecnicos = "SELECT id, nombre_usuario FROM usuarios WHERE rol = 'tecnico'";
            $result_tecnicos = $conn->query($sql_tecnicos);
            if ($result_tecnicos->num_rows > 0) {
                while($tecnico = $result_tecnicos->fetch_assoc()) {
                    echo '<option value="' . $tecnico['id'] . '">' . $tecnico['nombre_usuario'] . '</option>';
                }
            }
            ?>
        </select>
        <br>
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
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
        <br>
        <label for="observaciones">Observaciones:</label>
        <textarea id="observaciones" name="observaciones"></textarea>
        <br>
        <input type="submit" value="Generar Trabajo" class="btn">
        <a href="ver_trabajos.php" class="btn">Cancelar</a>
    </form>
</body>
</html>