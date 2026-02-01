<?php
require "config.php";
header("Content-Type: application/json; charset=utf-8");

$token  = $_GET["token"]  ?? "";
$sign   = $_GET["sign"]   ?? "";
$device = $_GET["device"] ?? "";

if ($token !== MDM_TOKEN || empty($device)) {
  echo json_encode(["command"=>"NONE","timestamp"=>0]);
  exit;
}

$expected = hash("sha256", MDM_TOKEN . MDM_SECRET);
if (!hash_equals($expected, $sign)) {
  echo json_encode(["command"=>"NONE","timestamp"=>0]);
  exit;
}

if (!file_exists(CMD_FILE)) {
  echo json_encode(["command"=>"NONE","timestamp"=>0]);
  exit;
}

$data = json_decode(file_get_contents(CMD_FILE), true);
if (!isset($data[$device])) {
  echo json_encode(["command"=>"NONE","timestamp"=>0]);
  exit;
}

echo json_encode($data[$device]);
