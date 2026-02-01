<?php
session_start();

if (!isset($_SESSION['nivel_acceso']) || $_SESSION['nivel_acceso'] != 2) {
    header("Location: login.php");
    exit();
}

// DB
$conn = new mysqli(
    "localhost",
    "luisfe19_kashyo30",
    "Kashyo1990.",
    "luisfe19_califa_DB"
);

if ($conn->connect_error) {
    die("Conexión fallida");
}

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("ID de usuario inválido.");
}

$user_id = (int)$_GET['user_id'];

$stmt = $conn->prepare("SELECT * FROM publicaciones WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicaciones del Usuario</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Estilos generales -->
</head>
<body>
    <header>
        <h1>Editar Publicaciones del Usuario</h1>
    </header>

    <nav>
        <a href="index.php">Inicio</a>
        <a href="dashboard_admin.php">Dashboard Admin</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <h2>Publicaciones del Usuario</h2>
            <div class="publications-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="publication">
                        <h3><?php echo htmlspecialchars($row['titulo']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($row['contenido'])); ?></p>
                        <p><strong>Fecha de Publicación:</strong> <?php echo htmlspecialchars($row['fecha']); ?></p>
                        <a href="editar_publicacion.php?id=<?php echo $row['id']; ?>" class="btn">Editar</a>
                        <!-- Opcional: Agregar botón para eliminar si se desea -->
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No hay publicaciones para este usuario.</p>
        <?php endif; ?>
    </div>

</body>
<footer>
    <div class="sub-footer">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <p>Copyright © 2024 Despacho Contable MD 
            
            - Desarrollado por: <a rel="nofollow noopener" href="https://api.whatsapp.com/send?phone=5216461797388&text=%F0%9F%91%8D%20Hola%2C%20Nuevo%20Cliente-Web%3A%0ANombre%3A%0ANumero%20Telefonico%3A%0AMensaje%3A%0A%0A" target="blank_" >KashyoTecnico</a></p>
          </div>
        </div>
      </div>
    </div>
</footer>
</html>

<?php
$conn->close();
?>
