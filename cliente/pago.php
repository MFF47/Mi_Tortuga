<?php
session_start();
include '../db.php';
include '../nombres_paginas.php';

$archivo = basename($_SERVER['PHP_SELF']);
$pagina = $nombres_paginas[$archivo] ?? $archivo;

$usuario = $_SESSION['username'] ?? 'Invitado';
$conn->query("INSERT INTO visitas (usuario, pagina) VALUES ('$usuario', '$pagina')");


if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$usuario = $_SESSION['username'];
$envios = $conn->query("SELECT * FROM metodos_envio WHERE activo = 1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Confirmar Pago - TecnoStore Guate</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>💳 Confirmar Pago</h2>
    <form method="POST" action="procesar_pago.php">
      <label>Correo electrónico:</label>
      <input type="email" name="correo" required>

      <label>Número de NIT:</label>
      <input type="text" name="nit" required>

      <label>Método de pago:</label>
      <select name="metodo_pago" required>
        <option value="Tarjeta de crédito">Tarjeta de crédito</option>
        <option value="PayPal">PayPal</option>
        <option value="Transferencia bancaria">Transferencia bancaria</option>
      </select><br><br>

      <label>Método de Envío:</label><br>
      <select name="metodo_envio" required>
        <?php while ($envio = $envios->fetch_assoc()): ?>
          <option value="<?= $envio['id'] ?>">
            <?= $envio['nombre'] ?> — Q<?= number_format($envio['base'], 2) ?> (<?= $envio['tiempo_estimado'] ?>)
          </option>
        <?php endwhile; ?>
      </select><br><br>

      <label>Valoración de tu experiencia:</label><br>
      <select name="valoracion">
        <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
        <option value="4">⭐⭐⭐⭐ Muy buena</option>
        <option value="3">⭐⭐⭐ Buena</option>
        <option value="2">⭐⭐ Regular</option>
        <option value="1">⭐ Mala</option>
      </select><br><br>

      <label>Comentario (opcional):</label>
      <textarea name="comentario" placeholder="¿Qué te pareció el proceso de compra?" rows="4" style="width:100%;"></textarea><br><br>

      <input type="checkbox" name="acepta_terminos" required>
      <label>Acepto los <a href="terminos_condiciones.php" target="_blank">Términos y Condiciones</a></label><br><br>

      <button type="submit">Finalizar Compra</button>
    </form>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
