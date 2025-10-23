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

// Actualizar datos básicos
$conn->query("UPDATE usuarios 
              SET username = '$nuevo_username', nombre = '$nombre', apellido = '$apellido' 
              WHERE username = '$usuario_actual'");

// Si se ingresó nueva contraseña, actualizarla
if (!empty($nueva_password)) {
  $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
  $conn->query("UPDATE usuarios SET password = '$password_hash' WHERE username = '$nuevo_username'");
}

// Actualizar sesión con nuevo username
$_SESSION['username'] = $nuevo_username;

header("Location: perfil.php");
exit();
