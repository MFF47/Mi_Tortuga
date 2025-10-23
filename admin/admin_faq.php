<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

// Eliminar pregunta
if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);
  $conn->query("DELETE FROM faq WHERE id = $id");
  header("Location: admin_faq.php");
  exit();
}

// Guardar respuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuesta'])) {
  $id = intval($_POST['id']);
  $respuesta = $conn->real_escape_string($_POST['respuesta']);
  $conn->query("UPDATE faq SET respuesta = '$respuesta' WHERE id = $id");
}

// Obtener todas las preguntas
$faqs = $conn->query("SELECT * FROM faq ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin FAQ</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>🛠️ Gestión de Preguntas Frecuentes</h2>

    <?php while ($f = $faqs->fetch_assoc()): ?>
      <div style="border:1px solid #ccc; padding:10px; margin-bottom:15px;">
        <p><strong>❓ <?= htmlspecialchars($f['pregunta']) ?></strong> <em>(<?= $f['usuario'] ?>)</em></p>

        <form method="POST" style="margin-top:10px;">
          <input type="hidden" name="id" value="<?= $f['id'] ?>">
          <textarea name="respuesta" rows="3" style="width:100%;" placeholder="Escribe la respuesta aquí..."><?= htmlspecialchars($f['respuesta']) ?></textarea><br>
          <button type="submit">💾 Guardar Respuesta</button>
          <a href="?eliminar=<?= $f['id'] ?>" onclick="return confirm('¿Eliminar esta pregunta?')">🗑️ Eliminar</a>
        </form>
      </div>
    <?php endwhile; ?>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
