<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$usuario = $_SESSION['username'];

$mis_reseñas = $conn->query("SELECT * FROM reseñas 
                             WHERE producto_id = 0 AND usuario = '$usuario' 
                             ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Soporte - TecnoStore Guate</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>📢 Tus reseñas generales</h2>

    <?php if ($mis_reseñas->num_rows > 0): ?>
      <?php while ($r = $mis_reseñas->fetch_assoc()): ?>
        <div class="reseña">
          <p><strong><?php echo $r['valoracion']; ?>⭐</strong> — <?php echo $r['comentario']; ?></p>
          <p><em>Estado: <?php echo ($r['aprobado'] ? '✅ Aprobada' : '⏳ Pendiente'); ?></em></p>
          <hr>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No has enviado reseñas aún.</p>
    <?php endif; ?>

    <h3>📝 Deja una nueva reseña</h3>
    <form method="POST" action="guardar_reseña_general.php">
      <label>Valoración:</label>
      <select name="valoracion" required>
        <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
        <option value="4">⭐⭐⭐⭐ Muy buena</option>
        <option value="3">⭐⭐⭐ Buena</option>
        <option value="2">⭐⭐ Regular</option>
        <option value="1">⭐ Mala</option>
      </select>

      <label>Comentario:</label>
      <textarea name="comentario" required placeholder="¿Cómo fue tu experiencia con TecnoStore Guate?" rows="4" style="width:100%;"></textarea>

      <button type="submit">Enviar reseña</button>
    </form>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
