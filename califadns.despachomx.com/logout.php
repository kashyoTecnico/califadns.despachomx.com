<?php
ini_set('session.cookie_domain', '.despachomx.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();

/* 🔥 LIMPIEZA TOTAL CALIFADNS */
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        '/',
        '.despachomx.com',
        false,
        true
    );
}

session_destroy();

/* ⛔ REDIRIGIR SIEMPRE A LOGIN MDM */
header("Location: https://mdm.despachomx.com/login.php");
exit;
