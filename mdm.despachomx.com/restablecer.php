<?php
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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);

    // Verificar validez del token
    $sql = "SELECT * FROM usuarios WHERE reset_token=? AND reset_token_expiry >= NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Mostrar el formulario para restablecer la contraseña
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Restablecer Contraseña</title>
            <link rel="stylesheet" href="styles.css">
            <style>
                /* Estilos adicionales específicos para esta página si es necesario */
            </style>
        </head>
        <body>
            <div class="message">
                <h2>Restablecer Contraseña</h2>
                <form action="restablecer.php" method="post">
                    <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
                    <label for="password">Nueva Contraseña:</label><br>
                    <input type="password" id="password" name="password" required><br><br>
                    
                    <input type="submit" value="Restablecer Contraseña">
                </form>
            </div>
        </body>
        </html>
        ';
    } else {
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Enlace de Recuperación Inválido</title>
            <link rel="stylesheet" href="styles.css">
            <style>
                /* Estilos adicionales específicos para esta página si es necesario */
            </style>
        </head>
        <body>
            <div class="message">
                <p>El enlace de recuperación no es válido o ha expirado.</p>
                <a href="login.php" class="btn">Ir a Iniciar sesión</a>
            </div>
        </body>
        </html>
        ';
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token']) && isset($_POST['password'])) {
    $token = $conn->real_escape_string($_POST['token']);
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Actualizar la contraseña en la base de datos
    $sql = "UPDATE usuarios SET password=?, reset_token=NULL, reset_token_expiry=NULL WHERE reset_token=? AND reset_token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $token);

    if ($stmt->execute()) {
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Contraseña Restablecida</title>
            <link rel="stylesheet" href="styles.css">
            <style>
                /* Estilos adicionales específicos para esta página si es necesario */
            </style>
        </head>
        <body>
            <div class="message">
                <p>Contraseña restablecida correctamente.</p>
                <a href="login.php" class="btn">Ir a Iniciar sesión</a>
            </div>
        </body>
        </html>
        ';
    } else {
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Error al Restablecer Contraseña</title>
            <link rel="stylesheet" href="styles.css">
            <style>
                /* Estilos adicionales específicos para esta página si es necesario */
            </style>
        </head>
        <body>
            <div class="message">
                <p>Error al restablecer la contraseña.</p>
                <a href="login.php" class="btn">Ir a Iniciar sesión</a>
            </div>
        </body>
        </html>
        ';
    }
}

$conn->close();
?>
