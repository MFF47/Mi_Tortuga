<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = intval($_GET['id']);
$conn->query("DELETE FROM productos WHERE id = $id");

header("Location: admin_productos.php");
exit();
