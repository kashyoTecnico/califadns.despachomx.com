<?php
session_start();

// Conectar a la base de datos
$servername = "localhost";
$username = "luisfe19_kashyo30";
$password = "Kashyo1990.";
$dbname = "luisfe19_califa_DB";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar variables para mensajes
$error_message = "";
$success_message = "";

// Procesar el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verificar si el usuario o el correo ya están en uso
    $checkStmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Usuario o correo electrónico ya están en uso
        $error_message = "Usuario o correo electrónico ya están en uso";
    } else {
        // Insertar el nuevo usuario en la base de datos
        $insertStmt = $conn->prepare("INSERT INTO usuarios (username, email, password) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $username, $email, $password);
        if ($insertStmt->execute()) {
            // Registro exitoso, redirigir a alguna página de bienvenida o confirmación
            $success_message = "Registro exitoso. Ahora puedes iniciar sesión.";
        } else {
            // Error al insertar
            $error_message = "Error al registrar el usuario";
        }
        $insertStmt->close();
    }
    $checkStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuarios</title>
    <style>
        body {
            background-color: #303030;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        form {
            margin: 20px auto;
            width: 300px;
            background-color: #444;
            padding: 20px;
            border-radius: 8px;
        }
        input[type="text"], input[type="password"], input[type="email"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #FFC107;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #FFB300;
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #555;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #777;
        }
        .message {
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
        }
        .error-message {
            background-color: #f44336;
            color: white;
        }
        .success-message {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Registro de Usuarios</h2>
    <form action="registro.php" method="post">
        <label for="username">Nombre de usuario:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="email">Correo Electrónico:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Registrarse">
    </form>
    <br>
    <a href="login.php" class="btn-back">Regresar a Iniciar sesión</a>

    <?php if ($error_message): ?>
        <div class="message error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="message success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
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
