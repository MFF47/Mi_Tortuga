<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$usuario_actual = $_SESSION['username'];
$nuevo_username = $conn->real_escape_string($_POST['nuevo_username']);
$nombre = $conn->real_escape_string($_POST['nombre']);
$apellido = $conn->real_escape_string($_POST['apellido']);
$nueva_password = $_POST['nueva_password'];


$conn->query("UPDATE usuarios 
              SET username = '$nuevo_username', nombre = '$nombre', apellido = '$apellido' 
              WHERE username = '$usuario_actual'");

if (!empty($nueva_password)) {
  $password_plana = $conn->real_escape_string($nueva_password);
  $conn->query("UPDATE usuarios SET password = '$password_plana' WHERE username = '$nuevo_username'");
}

$_SESSION['username'] = $nuevo_username;

header("Location: perfil.php");
exit();
