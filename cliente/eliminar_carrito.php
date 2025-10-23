<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$id = $_GET['id'];
$conn->query("DELETE FROM carrito WHERE id=$id");

header("Location: carrito.php");
exit();
