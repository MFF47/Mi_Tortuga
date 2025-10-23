<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$usuario = $_SESSION['username'];
$producto_id = $_GET['id'];

$sql = "SELECT * FROM carrito WHERE usuario='$usuario' AND producto_id=$producto_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $conn->query("UPDATE carrito SET cantidad = cantidad + 1 WHERE usuario='$usuario' AND producto_id=$producto_id");
} else {
  $conn->query("INSERT INTO carrito (usuario, producto_id, cantidad) VALUES ('$usuario', $producto_id, 1)");
}

header("Location: carrito.php");
exit();
