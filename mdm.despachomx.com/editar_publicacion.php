<?php
session_start();

if (!isset($_SESSION['id_usuario'], $_SESSION['nivel_acceso'])) {
    die("Sesión inválida.");
}

// Conectar a la base de datos
$conn = new mysqli(
    "localhost",
    "luisfe19_kashyo30",
    "Kashyo1990.",
    "luisfe19_califa_DB"
);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la publicación a editar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM publicaciones WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        die("Publicación no encontrada.");
    }

    $publicacion = $result->fetch_assoc();

    // Permisos
    if (
        $_SESSION['nivel_acceso'] != 2 &&
        $_SESSION['id_usuario'] != $publicacion['user_id']
    ) {
        die("No tienes permisos para editar esta publicación.");
    }

    $stmt->close();
} else {
    die("ID de publicación no válido.");
}

// Editar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editar_publicacion'])) {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $tema = $_POST['tema'];

    $updateStmt = $conn->prepare(
        "UPDATE publicaciones SET titulo = ?, contenido = ?, tema = ? WHERE id = ?"
    );
    $updateStmt->bind_param("sssi", $titulo, $contenido, $tema, $id);

    if ($updateStmt->execute()) {
        header("Location: index.php");
        exit();
    }

    $updateStmt->close();
}

// Eliminar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['eliminar_publicacion'])) {
    $deleteStmt = $conn->prepare("DELETE FROM publicaciones WHERE id = ?");
    $deleteStmt->bind_param("i", $id);

    if ($deleteStmt->execute()) {
        header("Location: index.php");
        exit();
    }

    $deleteStmt->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Publicación</title>
    <link rel="stylesheet" href="css/styles-admin.css"> <!-- Estilos generales -->
    <style>
        /* Estilos adicionales para centrar y justificar al centro */
        body {
            text-align: center;
        }
        .edit-container {
            display: inline-block;
            text-align: left;
            width: 80%;
            margin-top: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #555;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #777;
        }
        .btn-delete {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 0;
            background-color: #900;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-delete:hover {
            background-color: #c00;
        }
    </style>
</head>
<body>
    <header>
        <h1>Editar Publicación</h1>
    </header>

    <nav>
        <a href="index.php">Inicio</a>
        <?php if ($_SESSION['nivel_acceso'] == 2): ?>
            <a href="dashboard_admin.php">Dashboard Admin</a>
        <?php endif; ?>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <div class="edit-container">
        <form action="editar_publicacion.php?id=<?php echo $id; ?>" method="post">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($publicacion['titulo']); ?>" required>
            
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="5" required><?php echo htmlspecialchars($publicacion['contenido']); ?></textarea>
            
            <label for="tema">Tema:</label>
            <input type="text" id="tema" name="tema" value="<?php echo htmlspecialchars($publicacion['tema']); ?>" required>
            
            <input type="submit" name="editar_publicacion" value="Actualizar">
        </form>


        <form action="index.php?id=<?php echo $id; ?>" method="post">
                <input type="submit" name="volver_index" class="btn-back" value="Volver">
            </form>


        
    <?php if ($_SESSION['nivel_acceso'] == 2 || $_SESSION['id_usuario'] == $publicacion['user_id']): ?>
            <form action="editar_publicacion.php?id=<?php echo $id; ?>" method="post">
                <input type="submit" name="eliminar_publicacion" class="btn-delete" value="Eliminar Publicación">
            </form>
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
