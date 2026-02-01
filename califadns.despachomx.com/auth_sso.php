<?php
ini_set('session.cookie_domain', '.despachomx.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();


ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['token'])) {
    die("Token requerido");
}

$token = $_GET['token'];

$conn = new mysqli(
    "localhost",
    "luisfe19_kashyo30",
    "Kashyo1990.",
    "luisfe19_califa_DB"
);

if ($conn->connect_error) {
    die("Error de conexión");
}

$stmt = $conn->prepare("
    SELECT s.user_id, u.username, u.email
    FROM sso_tokens s
    INNER JOIN usuarios u ON u.id = s.user_id
    WHERE s.token = ?
      AND s.expires_at > NOW()
    LIMIT 1
");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    die("Token inválido o expirado");
}

$user = $res->fetch_assoc();

/* ✅ SESIÓN ÚNICA Y CONSISTENTE */
session_regenerate_id(true);

$_SESSION['califadns_auth'] = true;
$_SESSION['califadns_user'] = [
    'id'       => $user['user_id'],
    'username' => $user['username'],
    'email'    => $user['email']
];

/* 🔥 TOKEN DE UN SOLO USO */
$del = $conn->prepare("DELETE FROM sso_tokens WHERE token = ?");
$del->bind_param("s", $token);
$del->execute();

$stmt->close();
$del->close();
$conn->close();

/* 🚀 ENTRAR */
header("Location: index.php");
exit;
