<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Administrador</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Asegúrate de incluir los estilos CSS -->
</head>
<body>
    <div class="container">
        <div class="dashboard-box">
            <h2>Dashboard de Administrador</h2>
            <?php
            session_start();

            // Verificar si el usuario tiene una sesión activa y es administrador
            if (!isset($_SESSION['username']) || !isset($_SESSION['nivel_acceso']) || $_SESSION['nivel_acceso'] !== 'admin') {
                header("Location: login.html"); // Redirigir a la página de inicio de sesión si no es administrador
                exit();
            }

            // Mostrar información del administrador
            echo "<p>Bienvenido, " . $_SESSION['username'] . ".</p>";
            echo "<p>Este es tu panel de control donde puedes gestionar usuarios y otras funciones administrativas.</p>";

            // Enlaces para editar y bloquear usuarios
            echo "<br>";
            echo "<a href='editar_usuario.php' class='btn'>Editar Usuario</a>";
            echo "<a href='bloquear_usuario.php' class='btn'>Bloquear Usuario</a>";
            echo "<br><br>";
            echo "<a href='logout.php' class='btn'>Cerrar sesión</a>"; // Enlace para cerrar sesión
            ?>

            <!-- Botón para recuperar contraseña -->
            <br><br>
            <form action="enviar_recuperacion.php" method="post">
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="submit" value="Recuperar Contraseña" class="btn">
            </form>
        </div>
    </div>
</body>

<footer>
    <div class="sub-footer">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <p>Copyright © 2024 Despacho Contable MD 
            
            - Desarrollado por: <a rel="nofollow noopener" href="https://api.whatsapp.com/send?phone=5216461797388&text=%F0%9F%91%8D%20Hola%2C%20Nuevo%20Cliente-Web%3A%0ANombre%3A%0ANumero%20Telefonico%3A%0AMensaje%3A%0A%0A" target="blank_" >KashyoTecnico</a></p>
          </div>
        </div>
      </div>
    </div>
</footer>

</html>