<?php
require "config.php";
header("Content-Type: application/json");

$token  = $_GET["token"]  ?? "";
$device = $_GET["device"] ?? "";

if ($token !== MDM_TOKEN || empty($device)) {
    echo json_encode(["status"=>"UNKNOWN"]);
    exit;
}

$file = __DIR__ . "/devices.json";
if (!file_exists($file)) {
    echo json_encode(["status"=>"UNKNOWN"]);
    exit;
}

$data = json_decode(file_get_contents($file), true);

if (!isset($data[$device])) {
    echo json_encode(["status"=>"UNKNOWN"]);
    exit;
}

echo json_encode([
    "status" => $data[$device]["status"],
    "expires_at" => $data[$device]["expires_at"],
    "plan" => $data[$device]["plan"]
]);
