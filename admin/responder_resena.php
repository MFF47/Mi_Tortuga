<?php
session_start();
include '../db.php';

if ($_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = $_POST['id'];
$respuesta = $_POST['respuesta'];

$sql = "UPDATE reseñas SET respuesta = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $respuesta, $id);
$stmt->execute();

header("Location: admin_reseñas.php");
exit();
