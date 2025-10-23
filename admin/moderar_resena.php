<?php
session_start();
include '../db.php';

if ($_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = $_POST['id'];
$respuesta = $_POST['respuesta'];
$accion = $_POST['accion'];

$estado = ($accion === 'aprobar') ? 1 : 0;

$sql = "UPDATE reseñas SET respuesta = ?, aprobado = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $respuesta, $estado, $id);
$stmt->execute();

header("Location: admin_reseñas.php");
exit();
