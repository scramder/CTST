Este es un proyecto en PHP para la administración de servicios de un taller de reparacion de dispositivos electrónicos.

23/02/2025
Características establecidas:
   * Base de datos:
        - Clientes (datos personales del cliente)
        - Dispositivos (datos de los dispositivos asociados con el cliente)
        - Trabajos (datos de los trabajos creados y asignados a los técnicos)
        - Usuarios (datos de los usuarios y sus roles (administrador, mesa de entrada, técnicos)
   * Página del administrador
        - Header: Foto de perfil, Usuario activo, hora actual, boton para cerrar sesión.
        - Panel del administrador: Grilla con los ultimos trabajos, con filtro a través del estado del trabajo. Los botones de la derecha permiten ver (mediante un overlay), modificar y borrar trabajos.
        - Opciones:
             + Crear nuevo trabajo: Crea un cliente nuevo, con los datos del cliente creado, inicia la carga del dispositivo asociado al cliente, una vez creado el dispositivo, se inicia la carga de los datos del trabajo, asignando técnico, estado y observaciones.
             + Ver usuarios: Aceede a la página para administrar los usuarios del sistema.
             + Ver clientes: Accede a la página para administrar los clientes.
             + Ver trabajos: Accede a la página para administrar trabajos.


09/02/2025
Primeras características:

    * Ingreso de clientes
        - Datos personales
    * Ingreso de equipos
        - Datos del equipo
    * Asignación de trabajo
        - Asignación de trabajo por numero unico
        - Estados
    * Historial
    * Busqueda de clientes
    * Busqueda de equipos
    * Busqueda de trabajos
------------------------------------------

