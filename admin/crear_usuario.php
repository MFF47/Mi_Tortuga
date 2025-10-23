<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $conn->real_escape_string($_POST['username']);
  $nombre = $conn->real_escape_string($_POST['nombre']);
  $apellido = $conn->real_escape_string($_POST['apellido']);
  $rol = $conn->real_escape_string($_POST['rol']);
 $contraseña = $conn->real_escape_string($_POST['contraseña']);


  $sql = "INSERT INTO usuarios (username, nombre, apellido, rol, password) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssss", $username, $nombre, $apellido, $rol, $contraseña);
  $stmt->execute();

  header("Location: admin_usuarios.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Usuario</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>➕ Crear Nuevo Usuario</h2>
    <form method="POST">
      <label>Usuario:</label><br>
      <input type="text" name="username" required><br><br>

      <label>Nombre:</label><br>
      <input type="text" name="nombre" required><br><br>

      <label>Apellido:</label><br>
      <input type="text" name="apellido" required><br><br>

      <label>Rol:</label><br>
      <select name="rol" required>
        <option value="usuario">usuario</option>
        <option value="admin">Administrador</option>
      </select><br><br>

      <label>Contraseña:</label><br>
      <input type="password" name="contraseña" required><br><br>

      <button type="submit">Guardar Usuario</button>
    </form>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
