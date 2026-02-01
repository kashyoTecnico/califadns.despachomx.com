<?php
ini_set('session.cookie_domain', '.despachomx.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        '/',
        '.despachomx.com',
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

/* ⛔ REDIRECCIÓN FINAL */
header("Location: login.php");
exit;
