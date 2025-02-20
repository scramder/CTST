<?php
session_start();
session_unset();
session_destroy();
header("Location: index.html"); // Redirige a la página de inicio de sesión
exit();
?>