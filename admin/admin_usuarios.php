<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios</title>

  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>👥 Gestión de Usuarios</h2>
      <a href="crear_usuario.php"><button>➕ Crear Usuario</button></a>
    <table border="1" cellpadding="8" style="width:100%; margin-top:20px;">
      <tr>
        <th>ID</th><th>Usuario</th><th>Rol</th><th>Acciones</th>
      </tr>
      <?php while ($u = $usuarios->fetch_assoc()): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= $u['rol'] ?></td>
         <td>
  <a href="editar_usuario.php?id=<?= $u['id'] ?>">✏️ Editar</a> |
  <a href="cambiar_contraseña.php?id=<?= $u['id'] ?>">🔒 Contraseña</a> |
  <a href="eliminar_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">🗑️ Eliminar</a>
</td>

        </tr>
      <?php endwhile; ?>
    </table>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
