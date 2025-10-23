<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$usuario = $_SESSION['username'];
$valoracion = isset($_POST['valoracion']) ? intval($_POST['valoracion']) : 5;
$comentario = isset($_POST['comentario']) ? $conn->real_escape_string($_POST['comentario']) : '';

if (!empty($comentario)) {
  $sql = "INSERT INTO reseñas (usuario, producto_id, comentario, valoracion, aprobado) 
          VALUES (?, 0, ?, ?, NULL)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sii", $usuario, $comentario, $valoracion);
  $stmt->execute();
}

header("Location: soporte.php");
exit();
