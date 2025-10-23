<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$usuario = $_SESSION['username'];
$producto_id = intval($_POST['producto_id']);
$valoracion = intval($_POST['valoracion']);
$comentario = $conn->real_escape_string($_POST['comentario']);

// Insertar reseña como pendiente
$sql = "INSERT INTO reseñas (usuario, producto_id, comentario, valoracion, aprobado) 
        VALUES (?, ?, ?, ?, NULL)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisi", $usuario, $producto_id, $comentario, $valoracion);
$stmt->execute();

header("Location: producto.php?id=$producto_id");
exit();
