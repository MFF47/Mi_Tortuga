<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = intval($_GET['id']);
$usuario = $conn->query("SELECT * FROM usuarios WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $conn->real_escape_string($_POST['username']);
  $rol = $conn->real_escape_string($_POST['rol']);
  $nombre = $conn->real_escape_string($_POST['nombre']);
  $apellido = $conn->real_escape_string($_POST['apellido']);

  $conn->query("UPDATE usuarios SET username='$username', rol='$rol', nombre='$nombre', apellido='$apellido' WHERE id=$id");
  header("Location: admin_usuarios.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuario</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>✏️ Editar Usuario</h2>
    <form method="POST">
      <label>Usuario:</label><br>
      <input type="text" name="username" value="<?= htmlspecialchars($usuario['username']) ?>" required><br><br>

      <label>Nombre:</label><br>
      <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required><br><br>

      <label>Apellido:</label><br>
      <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required><br><br>

      <label>Rol:</label><br>
      <select name="rol" required>
        <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>usuario</option>
        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
      </select><br><br>

      <button type="submit">Actualizar</button>
    </form>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
