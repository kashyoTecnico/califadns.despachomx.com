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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nivel_acceso = $_POST['nivel_acceso'];

    // Actualizar el nivel de acceso del usuario
    $sql = "UPDATE usuarios SET nivel_acceso='$nivel_acceso' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Usuario actualizado correctamente.";
    } else {
        $message = "Error al actualizar el usuario: " . $conn->error;
    }
} else {
    // Obtener los datos del usuario
    $id = $_GET['id'];
    $sql = "SELECT * FROM usuarios WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        $message = "Usuario no encontrado.";
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="styles.css">
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
            margin-top: 20px;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="submit"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
            border-radius: 4px;
            background-color: #555;
            color: #fff;
        }
        input[type="submit"] {
            background-color: #FFC107;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #FFB300;
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
        .message {
            color: #fff;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-box">
            <h2>Editar Usuario</h2>
            <form action="editar_usuario.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" disabled ><br><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" disabled ><br><br>
                <label for="nivel_acceso">Nivel de Acceso:</label>
                <input type="text" id="nivel_acceso" name="nivel_acceso" value="<?php echo htmlspecialchars($row['nivel_acceso']); ?>"><br><br>
                <input type="submit" value="Actualizar">
            </form>
            <div class="message"><?php echo $message; ?></div>
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
