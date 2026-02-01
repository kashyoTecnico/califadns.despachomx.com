<?php
require "config.php";
header("Content-Type: application/json");

$token   = $_POST["token"]   ?? "";
$device  = $_POST["device"]  ?? "";
$status  = $_POST["status"]  ?? null;
$expires = $_POST["expires"] ?? null;
$plan    = $_POST["plan"]    ?? null;

if ($token !== MDM_TOKEN || empty($device)) {
    http_response_code(403);
    echo json_encode(["error"=>"invalid"]);
    exit;
}

$file = __DIR__ . "/devices.json";
if (!file_exists($file)) {
    echo json_encode(["error"=>"no devices"]);
    exit;
}

$data = json_decode(file_get_contents($file), true);

if (!isset($data[$device])) {
    echo json_encode(["error"=>"device not found"]);
    exit;
}

if ($status !== null)  $data[$device]["status"] = $status;
if ($expires !== null) $data[$device]["expires_at"] = (int)$expires;
if ($plan !== null)    $data[$device]["plan"] = $plan;

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

echo json_encode(["status"=>"ok"]);
