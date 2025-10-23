<?php
session_start();
include '../db.php';

if ($_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = $_POST['id'];
$respuesta = $_POST['respuesta'] ?? null;

$sql = "UPDATE reseñas SET aprobado = 1, respuesta = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $respuesta, $id);
$stmt->execute();

header("Location: admin_reseñas.php");
exit();
