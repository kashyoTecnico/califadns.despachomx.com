<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['nivel_acceso']) || $_SESSION['nivel_acceso'] != 2) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli(
    "localhost",
    "luisfe19_kashyo30",
    "Kashyo1990.",
    "luisfe19_califa_DB"
);

if ($conn->connect_error) {
    die("Conexión fallida");
}

// BORRAR
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_user_id'])) {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_POST['delete_user_id']);
    $stmt->execute();
    $stmt->close();
}

// DESBLOQUEAR
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['unlock_user_id'])) {
    $stmt = $conn->prepare("UPDATE usuarios SET nivel_acceso = 1 WHERE id = ?");
    $stmt->bind_param("i", $_POST['unlock_user_id']);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM usuarios");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>

<style>
body {
    background:#303030;
    color:white;
    font-family:Arial;
    padding:20px;
}
.container { max-width:960px; margin:auto; }
.dashboard-box { background:#444; padding:20px; border-radius:8px; }
table { width:100%; border-collapse:collapse; background:#555; margin-top:20px; }
th,td { padding:10px; border-bottom:1px solid #777; }
th { background:#333; }
.btn {
    background:#4CAF50;
    color:white;
    padding:8px 14px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    text-decoration:none;
}
.btn-delete { background:#f44336; }
.btn-unlock { background:#008CBA; }
.sub-footer {
    background:#333;
    position:fixed;
    bottom:0;
    width:100%;
    text-align:center;
    padding:8px;
}
</style>
</head>

<body>
<div class="container">
<div class="dashboard-box">

<h2>Dashboard de Administrador</h2>
<p>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?></p>

<table>
<thead>
<tr>
<th>ID</th><th>Usuario</th><th>Email</th><th>Nivel</th><th>Acciones</th>
</tr>
</thead>
<tbody>
<?php while ($u = $result->fetch_assoc()): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= $u['nivel_acceso'] ?></td>
<td>
<a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn">Editar</a>

<form method="post" style="display:inline">
<input type="hidden" name="delete_user_id" value="<?= $u['id'] ?>">
<button class="btn btn-delete">Borrar</button>
</form>

<?php if ($u['nivel_acceso']==0): ?>
<form method="post" style="display:inline">
<input type="hidden" name="unlock_user_id" value="<?= $u['id'] ?>">
<button class="btn btn-unlock">Desbloquear</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<br>

<!-- 🔥 BOTÓN CORREGIDO -->
<button type="button" class="btn" onclick="goCalifa()">Ir a CalifaDNS</button>

<br><br>

<a href="index.php" class="btn">Ir a Blog</a>
<a href="logout.php" class="btn btn-delete">Cerrar sesión</a>

</div>
</div>

<script>
async function goCalifa() {
    try {
        const res = await fetch("sso/create_token.php");
        const text = await res.text();
        const data = JSON.parse(text);

        if (!data.token) {
            alert("SSO inválido");
            return;
        }

        window.location.href =
            "https://califadns.despachomx.com/auth_sso.php?token=" + data.token;

    } catch (e) {
        alert("Error SSO: " + e);
    }
}
</script>


<footer class="sub-footer">
© 2024 Despacho Contable MD
</footer>
</body>
</html>

<?php $conn->close(); ?>
