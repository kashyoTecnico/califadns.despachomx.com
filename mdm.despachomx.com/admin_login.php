<?php
session_start();

// =======================
// CONEXIÓN A LA BD
// =======================
$conn = new mysqli(
    "localhost",
    "luisfe19_kashyo30",
    "Kashyo1990.",
    "luisfe19_califa_DB"
);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// =======================
// PROCESAR LOGIN ADMIN
// =======================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Buscar por EMAIL (como ya lo usas)
    $stmt = $conn->prepare("
        SELECT id, username, email, password, nivel_acceso, created_at
        FROM usuarios
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {

        $row = $res->fetch_assoc();

        // =======================
        // VERIFICAR CONTRASEÑA
        // =======================
        if (!password_verify($password, $row['password'])) {
            header("Location: admin_login.html?error=login");
            exit();
        }

        // =======================
        // VERIFICAR ADMIN
        // =======================
        if ((int)$row['nivel_acceso'] !== 2) {
            echo "No tienes permisos de administrador.";
            exit();
        }

        // =======================
        // CREAR SESIÓN (UNA SOLA VEZ)
        // =======================
        $_SESSION['id_usuario']   = $row['id'];
        $_SESSION['username']     = $row['username'];
        $_SESSION['email']        = $row['email'];
        $_SESSION['nivel_acceso'] = (int)$row['nivel_acceso'];
        $_SESSION['created_at']   = $row['created_at'];

        // =======================
        // REDIRIGIR
        // =======================
        header("Location: dashboard_admin.php");
        exit();
    }

    header("Location: admin_login.html?error=login");
    exit();
}

$conn->close();
