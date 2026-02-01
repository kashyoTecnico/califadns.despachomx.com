<?php
require "config.php";
header("Content-Type: application/json");

$token  = $_POST["token"]  ?? "";
$device = $_POST["device"] ?? "";
$model  = $_POST["model"]  ?? "unknown";
$android= $_POST["android"]?? "unknown";

if ($token !== MDM_TOKEN || empty($device)) {
    http_response_code(403);
    echo json_encode(["error"=>"invalid"]);
    exit;
}

$file = __DIR__ . "/devices.json";
$devices = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!isset($devices[$device])) {
    // 🆕 nuevo dispositivo → 3 días de prueba
    $devices[$device] = [
        "device_id"  => $device,
        "model"      => $model,
        "android"    => $android,
        "status"     => "ACTIVE",
        "plan"       => "TRIAL",
        "expires_at" => time() + (3 * 86400),
        "last_seen"  => time()
    ];
} else {
    $devices[$device]["last_seen"] = time();
}

// ⛔ expiración automática
if ($devices[$device]["expires_at"] < time()) {
    $devices[$device]["status"] = "EXPIRED";
}

file_put_contents($file, json_encode($devices, JSON_PRETTY_PRINT));

echo json_encode([
    "status" => $devices[$device]["status"],
    "expires_at" => $devices[$device]["expires_at"]
]);
