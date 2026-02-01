<?php
ini_set('session.cookie_domain', '.despachomx.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode([
        "error" => "No session"
    ]);
    exit;
}

$conn = new mysqli(
    "localhost",
    "luisfe19_kashyo30",
    "Kashyo1990.",
    "luisfe19_califa_DB"
);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "error" => "DB error"
    ]);
    exit;
}

$user_id = (int) $_SESSION['id_usuario'];
$token   = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$stmt = $conn->prepare("
    INSERT INTO sso_tokens (user_id, token, expires_at)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iss", $user_id, $token, $expires);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        "error" => "Insert failed"
    ]);
    exit;
}

echo json_encode([
    "token" => $token
]);

$stmt->close();
$conn->close();
exit;
