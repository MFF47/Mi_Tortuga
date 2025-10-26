<?php
session_start();
include 'db.php';


$username = $_POST['username'];
$password = $_POST['password'];


$sql = "SELECT * FROM usuarios WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $usuario = $result->fetch_assoc();

  if ($password === $usuario['password']) {
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['rol'] = $usuario['rol'];


    if ($usuario['rol'] === 'admin') {
      header("Location: admin/dashboard.php");
    } else {
      header("Location: cliente/dashboard.php");
    }
    exit();
  } else {
    echo "<script>alert('Contraseña incorrecta'); window.location.href='index.html';</script>";
  }
} else {
  echo "<script>alert('Usuario no encontrado'); window.location.href='index.html';</script>";
}
?>
