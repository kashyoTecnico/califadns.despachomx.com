<?php
session_start();

// Verificar si el usuario tiene una sesión activa y es administrador (nivel_acceso = 2)
if (!isset($_SESSION['username']) || !isset($_SESSION['nivel_acceso']) || $_SESSION['nivel_acceso'] != 2) {
    header("Location: login.html");
    exit();
}

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

$message = ""; // Variable para almacenar el mensaje de resultado

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Bloquear usuario (puedes cambiar el nivel de acceso a un valor específico para representar un bloqueo)
    $sql = "UPDATE usuarios SET nivel_acceso=0 WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Usuario bloqueado correctamente.";
    } else {
        $message = "Error al bloquear el usuario: " . $conn->error;
    }
} else {
    $message = "ID de usuario no proporcionado.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloquear Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
            color: #fff;
            text-align: center;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-box {
            background-color: #444;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #555;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-box">
            <h2>Bloquear Usuario</h2>
            <p><?php echo $message; ?></p>
            <a href="dashboard_admin.php" class="btn">Volver al Dashboard</a>
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

