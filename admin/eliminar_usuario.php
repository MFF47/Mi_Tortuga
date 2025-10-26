<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = intval($_GET['id']);


$usuario = $conn->query("SELECT username FROM usuarios WHERE id = $id")->fetch_assoc();
if ($usuario && $usuario['username'] === $_SESSION['username']) {
  echo "<script>alert('No puedes eliminar tu propio usuario.'); window.location.href='admin_usuarios.php';</script>";
  exit();
}

$conn->query("DELETE FROM usuarios WHERE id = $id");
header("Location: admin_usuarios.php");
exit();
?>
