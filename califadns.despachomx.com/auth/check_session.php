<?php
session_start();

/*
  Sesión válida = viene del SSO
*/
if (
    !isset($_SESSION['sso_user_id']) ||
    !isset($_SESSION['sso_valid']) ||
    $_SESSION['sso_valid'] !== true
) {
    header("Location: https://mdm.despachomx.com/login.php");
    exit();
}
