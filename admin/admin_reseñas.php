<?php
session_start();
include '../db.php';
include 'header.php';

if ($_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

function mostrarEstrellas($valoracion) {
  return str_repeat('⭐', $valoracion) . str_repeat('☆', 5 - $valoracion);
}

$result = $conn->query("SELECT * FROM reseñas ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Moderación de Reseñas</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<h2 class="titulo-reseñas">📝 Moderación de reseñas</h2>
<table class="tabla-reseñas">
  <thead>
    <tr>
      <th>Usuario</th>
      <th>Comentario</th>
      <th>Valoración</th>
      <th>Respuesta</th>
      <th>Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['usuario']) ?></td>
        <td><?= htmlspecialchars($row['comentario']) ?></td>
        <td><?= mostrarEstrellas($row['valoracion']) ?></td>
        <td><?= !empty($row['respuesta']) ? htmlspecialchars($row['respuesta']) : '—' ?></td>
        <td>
          <?php if (empty($row['respuesta'])): ?>
            <form method="POST" action="responder_resena.php">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <textarea name="respuesta" placeholder="Escribe una respuesta para el cliente" required></textarea>
              <button type="submit">Responder</button>
            </form>
          <?php else: ?>
            —
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
