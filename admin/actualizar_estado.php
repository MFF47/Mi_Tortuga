<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = intval($_POST['id']);
$estado = $conn->real_escape_string($_POST['estado_envio']);

$conn->query("UPDATE pedidos SET estado_envio='$estado' WHERE id=$id");
header("Location: admin_pedidos.php");
exit();
?>
