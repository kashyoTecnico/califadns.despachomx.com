<?php
ini_set('session.cookie_domain', '.despachomx.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();


if (!isset($_SESSION['id_usuario'], $_SESSION['nivel_acceso'])) {
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

// Nueva publicación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nueva_publicacion'])) {
    $user_id = $_SESSION['id_usuario'];
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $tema = $_POST['tema'];

    $stmt = $conn->prepare(
        "INSERT INTO publicaciones (user_id, titulo, contenido, tema) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isss", $user_id, $titulo, $contenido, $tema);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}

// Publicaciones
$sql = "
SELECT p.*, u.username 
FROM publicaciones p 
JOIN usuarios u ON p.user_id = u.id 
ORDER BY p.fecha DESC
";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Blog</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #222; /* Fondo oscuro similar al login */
            color: #fff; /* Texto blanco para mejor contraste */
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative;
        }
        nav {
            display: flex;
            justify-content: center;
            background-color: #333;
            width: 100%;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #fff; /* Texto blanco para mejor contraste */
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        nav a:hover {
            background-color: #555;
        }
        .welcome-message, .user-info, .post-container {
            background-color: rgba(255, 255, 255, 0.1); /* Fondo semi-transparente */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 80%;
            max-width: 800px;
            margin: 20px 0;
        }
        .post-container form {
            display: flex;
            flex-direction: column;
        }
        .post-container label {
            margin-top: 10px;
        }
        .post-container input[type="text"], .post-container textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .post-container input[type="submit"] {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .post-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .post {
            border-top: 1px solid #ddd;
            padding: 15px 0;
        }
        .post-title {
            font-weight: bold;
            font-size: 1.2em;
        }
        .post-content {
            margin-top: 10px;
        }
        .post-footer {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .post-footer small {
            color: #777;
        }
        .btn {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .logout:hover {
            background-color: #d32f2f;
        }
        .CalifaDNS {
            position: absolute;
            top: 80px;
            right: 44px;
            background-color: #00FFFF;
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .CalifaDNS:hover {
            background-color: #E0FFFF;
        }		
        .btn-back {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #666;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido al Blog</h1>
        <a href="logout.php" class="logout">Cerrar sesión</a>
		<a href="https://califadns.despachomx.com/index.php" class="CalifaDNS">CalifaDNS</a>
    </header>

    <nav>
        <a href="index.php">Inicio</a>
        <a href="https://minegocioclip.mx/despachocontablemd/catalogo">Publicaciones</a>
        <a href="https://despachomx.uk/about.html">Acerca de</a>
        <a href="https://despachomx.uk/contact.html">Contacto</a>
        <?php if ($_SESSION['nivel_acceso'] == 2): ?>
            <a href="dashboard_admin.php">Dashboard Admin</a>
        <?php endif; ?>
    </nav>

    <div class="welcome-message">
        <?php
        if (isset($_SESSION['username'])) {
            echo "<h2>Bienvenido, " . $_SESSION['username'] . "!</h2>";
            echo "<p>Tu nivel de acceso es: " . $_SESSION['nivel_acceso'] . "</p>";
        }
        ?>
    </div>

<div class="user-info">
    <p>Usuario: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <p>Correo: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    <p>Nivel de Acceso: <?php echo $_SESSION['nivel_acceso']; ?></p>
    <p>Registrado desde: <?php echo $_SESSION['created_at']; ?></p>
</div>



    <div class="post-container">
        <form action="index.php" method="post">
            <h3>Nueva Publicación</h3>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>
            
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="5" required></textarea>
            
            <label for="tema">Tema:</label>
            <input type="text" id="tema" name="tema" required>
            
            <input type="submit" name="nueva_publicacion" value="Publicar">
        </form>

        <h3>Publicaciones Recientes</h3>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <div class="post-title"><?php echo htmlspecialchars($row['titulo']); ?></div>
                    <div class="post-content"><?php echo htmlspecialchars($row['contenido']); ?></div>
                    <div class="post-footer">
                        <small>Publicado por <?php echo htmlspecialchars($row['username']); ?> el <?php echo htmlspecialchars($row['fecha']); ?></small>
                        <?php if ($_SESSION['nivel_acceso'] == 2 || $_SESSION['id_usuario'] == $row['user_id']): ?>
                            <a href="editar_publicacion.php?id=<?php echo $row['id']; ?>" class="btn">Editar</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay publicaciones disponibles.</p>
        <?php endif; ?>
    </div>
</body>
<footer>
    <div class="row">
        <div class="col-md-12">
            <p>Copyright © 2024 Despacho Contable MD 
                - Desarrollado por: <a rel="nofollow noopener" href="https://api.whatsapp.com/send?phone=5216461797388&text=%F0%9F%91%8D%20Hola%2C%20Nuevo%20Cliente-Web%3A%0ANombre%3A%0ANumero%20Telefonico%3A%0AMensaje%3A%0A%0A" target="blank_">KashyoTecnico</a>
            </p>
        </div>
    </div>
</footer>

</html>