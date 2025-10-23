<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

if (isset($_POST['cantidades'])) {
  foreach ($_POST['cantidades'] as $id => $cantidad) {
    $cantidad = max(1, intval($cantidad)); // mínimo 1
    $conn->query("UPDATE carrito SET cantidad = $cantidad WHERE id = $id");
  }
}

header("Location: carrito.php");
exit();
