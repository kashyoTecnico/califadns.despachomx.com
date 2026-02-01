<?php
session_start();

if (!isset($_SESSION['nivel_acceso']) || $_SESSION['nivel_acceso'] != 2) {
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_post_id'])) {
    $postId = (int)$_POST['delete_post_id'];

    $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}

$conn->close();
