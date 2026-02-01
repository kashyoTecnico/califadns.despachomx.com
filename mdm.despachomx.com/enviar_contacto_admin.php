<?php
use PHPMailer/PHPMailer/PHPMailer;
use PHPMailer/PHPMailer/Exception;

require 'vendor/autoload.php';

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

// Procesar el formulario de contacto al administrador
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $usuario = $conn->real_escape_string($_POST['usuario']);

    // Correo del administrador (tu dirección de correo)
    $admin_email = 'inboxto.u@gmail.com';

    // Asunto del correo
    $asunto = 'Contacto de usuario con acceso no estándar';

    // Construir el cuerpo del mensaje
    $mensaje = "Se ha recibido un mensaje de un usuario con acceso no estándar:/n/n";
    $mensaje .= "Nombre: $nombre/n";
    $mensaje .= "Correo: $correo/n";
    $mensaje .= "Usuario: $usuario/n";

    // Enviar el correo electrónico
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'inboxto.u@gmail.com'; // Coloca tu dirección de correo aquí
        $mail->Password = 'cgjq dvzc aasj lurw'; // Coloca tu contraseña aquí
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('inboxto.u@gmail.com', 'Despacho MD'); // Dirección y nombre del remitente
        $mail->addAddress($admin_email); // Dirección del destinatario (administrador)

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = nl2br($mensaje);

        // Enviar correo electrónico
        $mail->send();
        header("Location: contactar_admin.html?enviado=true");
        exit();
    } catch (Exception $e) {
        header("Location: contactar_admin.html?error=envio_failed");
        exit();
    }
} else {
    header("Location: contactar_admin.html");
    exit();
}

$conn->close();
?>
