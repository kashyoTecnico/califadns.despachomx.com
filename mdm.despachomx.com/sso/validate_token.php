<?php
$conn = new mysqli(
    "localhost",
    "luisfe19_kashyo30",
    "Kashyo1990.",
    "luisfe19_califa_DB"
);

if ($conn->connect_error) {
    http_response_code(500);
    exit;
}

$token = $_GET['token'] ?? '';

$stmt = $conn->prepare("
    SELECT u.id, u.username, u.email
    FROM sso_tokens s
    JOIN usuarios u ON u.id = s.user_id
    WHERE s.token = ?
      AND s.used = 0
      AND s.expires_at > NOW()
    LIMIT 1
");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    http_response_code(403);
    exit("Token inválido");
}

$user = $res->fetch_assoc();

$conn->query("UPDATE sso_tokens SET used = 1 WHERE token = '$token'");

echo json_encode($user);
