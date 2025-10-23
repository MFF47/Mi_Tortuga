<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nueva = $conn->real_escape_string($_POST['nueva']); // sin hash
  $conn->query("UPDATE usuarios SET password='$nueva' WHERE id=$id");
  header("Location: admin_usuarios.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cambiar Contraseña</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>🔒 Cambiar Contraseña</h2>
    <form method="POST">
      <label>Nueva contraseña:</label>
      <input type="password" name="nueva" required>
      <button type="submit">Actualizar</button>
    </form>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
