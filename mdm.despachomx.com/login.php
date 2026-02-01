<?php
ini_set('session.cookie_domain', '.despachomx.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();


$conn = new mysqli(
    "localhost",
    "luisfe19_kashyo30",
    "Kashyo1990.",
    "luisfe19_califa_DB"
);

if ($conn->connect_error) {
    die("Conexión fallida");
}

// Procesar el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Buscar el usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, username, password, nivel_acceso, email FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Verificar la contraseña
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Iniciar sesión y redirigir al usuario según nivel de acceso
            $_SESSION['username'] = $row['username'];
            $_SESSION['nivel_acceso'] = $row['nivel_acceso'];
            $_SESSION['nombre_completo'] = $row['nombre_completo'];
            $_SESSION['correo'] = $row['correo'];
            $_SESSION['id_usuario'] = $row['id']; // opcional: almacenar el ID del usuario

            // Redirigir según el nivel de acceso
            if ($row['nivel_acceso'] == 1) {
                header("Location: bienvenida_usuario.php"); // Redirigir a página de bienvenida para nivel 1
                exit();
            } elseif ($row['nivel_acceso'] == 2) {
                header("Location: dashboard_admin.php"); // Redirigir al panel de administrador para nivel 2
                exit();
            } else {
                // Manejar otros niveles de acceso según sea necesario
                header("Location: contactar_admin.html"); // Redirigir a página de contacto del administrador
                exit();
            }
        } else {
            // Contraseña incorrecta
            header("Location: login.php?error=login_failed");
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: login.php?error=login_failed");
        exit();
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>

    <!-- Botón de inicio de sesión de administrador -->
    <div class="top-right">
        <a href="admin_login.html" class="admin-login-btn">Admin Login</a>
    </div>

    <style>
        /* Estilos CSS para centrar y dar estilo */
        body {
            background-color: #303030;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }

        .admin-login-btn {
            color: white;
        }

        form {
            margin: 20px auto;
            width: 300px;
            background-color: #444;
            padding: 20px;
            border-radius: 8px;

           
        }

        input[type="text"], input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .btn-recover {
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-recover:hover {
            background-color: #da190b;
        }

        .btn-register {
            background-color: #FFEB3B;
            color: black;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-register:hover {
            background-color: #FBC02D;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .welcome-message {
            color: green;
            margin-top: 10px;
        }

    
        
    </style>


</head>
<body>

    <h2>Iniciar sesión</h2>

    <!-- Formulario de inicio de sesión -->
    <form action="login.php" method="post">
        <label for="username">Nombre de usuario:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Iniciar sesión">
    </form>

    <!-- Mensaje de error -->
    <?php if(isset($_GET['error']) && $_GET['error'] == 'login_failed'): ?>
        <div class="error-message">Usuario o contraseña incorrectos.</div>
    <?php endif; ?>

    <!-- Botón de recuperación de contraseña -->
    <button class="btn-recover" onclick="window.location.href='recuperar.html'">Recuperar Contraseña</button>

    <!-- Botón de registrarse -->
    <button class="btn-register" onclick="window.location.href='registro.php'">Registrarse</button>

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
