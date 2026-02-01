<?php
header("Content-Type: application/json");

echo file_exists("devices.json")
    ? file_get_contents("devices.json")
    : "{}";
