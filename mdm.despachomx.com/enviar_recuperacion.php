<?php
// =======================
// DEBUG TEMPORAL (HOSTING)
// =======================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =======================
// PHPMailer
// =======================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// =======================
// AUTLOAD SEGURO (NO ROMPE)
// =======================
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    die("ERROR: PHPMailer no está instalado. Falta /vendor/autoload.php");
}
require $autoload;

// =======================
// CONEXIÓN BD
// =======================
$servername = "localhost";
$username   = "luisfe19_kashyo30";
$password   = "Kashyo1990.";
$dbname     = "luisfe19_califa_DB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// =======================
// PROCESAR FORMULARIO
// =======================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['email']) || empty($_POST['email'])) {
        die("Email no recibido");
    }

    $email = $conn->real_escape_string($_POST['email']);

    // Buscar usuario
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {

        // Token
        $token  = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 day"));

        // Guardar token
        $sql_update = "
            UPDATE usuarios 
            SET reset_token='$token', reset_token_expiry='$expiry' 
            WHERE email='$email'
        ";

        if ($conn->query($sql_update) === TRUE) {

            $mail = new PHPMailer(true);

            try {
                // =======================
                // SMTP
                // =======================
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'inboxto.u@gmail.com';
                $mail->Password   = 'cgjq dvzc aasj lurw';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // =======================
                // CORREO
                // =======================
                $mail->setFrom('inboxto.u@gmail.com', 'Tu Despacho MX');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de Contraseña';
                $mail->Body = "
                    Para restablecer su contraseña, haga clic en el siguiente enlace:<br><br>
                    <a href='https://mdm.despachomx.com/restablecer.php?token=$token'>
                        Restablecer contraseña
                    </a>
                ";

                $mail->send();

                // =======================
                // HTML ÉXITO (TUYO)
                // =======================
                echo '<!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="refresh" content="5;url=login.php">
                    <title>Recuperación Enviada</title>
                    <style>
                        body {
                            background-color: #303030;
                            color: white;
                            font-family: Arial, sans-serif;
                            text-align: center;
                            padding: 50px;
                        }
                        .message {
                            background-color: #444;
                            padding: 20px;
                            border-radius: 8px;
                            width: 300px;
                            margin: 0 auto;
                        }
                        .btn {
                            display: inline-block;
                            padding: 10px 20px;
                            margin-top: 10px;
                            background-color: #555;
                            color: #fff;
                            text-decoration: none;
                            border-radius: 5px;
                        }
                    </style>
                </head>
                <body>
                    <div class="message">
                        <p>Se ha enviado un enlace de recuperación a su correo electrónico.</p>
                        <a href="login.php" class="btn">Ir a Iniciar sesión</a>
                    </div>
                </body>
                </html>';
                exit();

            } catch (Exception $e) {
                die("Error al enviar correo: " . $mail->ErrorInfo);
            }

        } else {
            die("Error al guardar token: " . $conn->error);
        }

    } else {
        die("No se encontró ningún usuario con ese correo.");
    }
}

$conn->close();
?>
