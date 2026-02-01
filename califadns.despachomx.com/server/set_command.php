<?php
require "config.php";
header("Content-Type: application/json");

$token  = $_POST["token"]  ?? "";
$device = $_POST["device"] ?? "";
$cmd    = $_POST["cmd"]    ?? "";

$allowed = [
  "DEV_TEMP_ON","DEV_TEMP_OFF",
  "ENTER_KIOSK","EXIT_KIOSK",
  "WIFI_LOCK","WIFI_UNLOCK",
  "DNS_LOCK","DNS_UNLOCK","DNS_SET","DNS_ENABLE",
  "FR_LOCK","FR_UNLOCK",
  "STATUSBAR_LOCK","STATUSBAR_UNLOCK",
  "UPDATES_OFF","UPDATES_ON",
  "REBOOT","FACTORY_RESET"
];

if ($token !== MDM_TOKEN || empty($device)) {
  http_response_code(403);
  echo json_encode(["error"=>"invalid token or device"]);
  exit;
}

if (!in_array($cmd, $allowed)) {
  http_response_code(400);
  echo json_encode(["error"=>"invalid command"]);
  exit;
}

/* cargar comandos existentes */
$data = [];
if (file_exists(CMD_FILE)) {
  $json = file_get_contents(CMD_FILE);
  $data = json_decode($json, true) ?: [];
}

/* guardar SOLO para este dispositivo */
$data[$device] = [
  "command"   => $cmd,
  "timestamp" => time()
];

file_put_contents(CMD_FILE, json_encode($data, JSON_PRETTY_PRINT));

echo json_encode([
  "status" => "ok",
  "device" => $device,
  "cmd"    => $cmd
]);
