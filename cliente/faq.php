<?php
session_start();
include '../db.php';


include '../nombres_paginas.php';

$archivo = basename($_SERVER['PHP_SELF']);
$pagina = $nombres_paginas[$archivo] ?? $archivo;

$usuario = $_SESSION['username'] ?? 'Invitado';
$conn->query("INSERT INTO visitas (usuario, pagina) VALUES ('$usuario', '$pagina')");



$usuario = $_SESSION['username'] ?? 'Anónimo';

// Insertar nueva pregunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pregunta'])) {
  $pregunta = $conn->real_escape_string($_POST['pregunta']);
  $conn->query("INSERT INTO faq (usuario, pregunta) VALUES ('$usuario', '$pregunta')");
}

// Obtener preguntas con o sin respuesta
$faqs = $conn->query("SELECT * FROM faq ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Preguntas Frecuentes</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>❓ Preguntas Frecuentes</h2>

    <form method="POST">
      <label>¿Tienes una duda? Escríbela aquí:</label><br>
      <textarea name="pregunta" required rows="3" style="width:100%;"></textarea><br>
      <button type="submit">Enviar Pregunta</button>
    </form>

    <hr>

    <?php while ($f = $faqs->fetch_assoc()): ?>
      <div style="margin-bottom:20px;">
        <p><strong>🗨️ <?= htmlspecialchars($f['pregunta']) ?></strong></p>
        <?php if ($f['respuesta']): ?>
          <p style="margin-left:20px;">💬 <?= nl2br(htmlspecialchars($f['respuesta'])) ?></p>
        <?php else: ?>
          <p style="margin-left:20px; color:gray;">(En espera de respuesta...)</p>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
