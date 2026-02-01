<?php
session_start();

// Solo usuarios normales (nivel 1)
if (!isset($_SESSION['id_usuario']) || $_SESSION['nivel_acceso'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Bienvenida</title>

<style>
body {
    background:#333;
    color:#fff;
    font-family:Arial;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.box {
    background:#444;
    padding:30px;
    border-radius:10px;
    text-align:center;
}
.btn {
    display:block;
    margin:10px auto;
    padding:10px 20px;
    background:#4CAF50;
    color:white;
    text-decoration:none;
    border-radius:5px;
}
.btn.red { background:#f44336; }
.btn.blue { background:#2196F3; }
</style>
</head>

<body>
<div class="box">
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?></h2>
    <p>Acceso de usuario</p>

    <!-- 🔐 ENTRAR A CALIFADNS -->
    <button class="btn blue" onclick="goCalifa()">Ir a CalifaDNS</button>

    <!-- BLOG -->
    <a href="index.php" class="btn">Ir al Blog</a>

    <!-- LOGOUT -->
    <a href="logout.php" class="btn red">Cerrar sesión</a>
</div>

<script>
async function goCalifa() {
    const res = await fetch("sso/create_token.php");
    const data = await res.json();

    if (!data.token) {
        alert("No se pudo iniciar SSO");
        return;
    }

    window.location.href =
        "https://califadns.despachomx.com/auth_sso.php?token=" + data.token;
}
</script>

</body>
</html>
