<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

// Obtener todos los pedidos con datos relevantes
$pedidos = $conn->query("
  SELECT p.*, m.nombre AS metodo_envio 
  FROM pedidos p
  LEFT JOIN metodos_envio m ON p.metodo_envio_id = m.id
  ORDER BY p.fecha ASC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Pedidos</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>📦 Administración de Pedidos</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Correo</th>
        <th>NIT</th>
        <th>Pago</th>
        <th>Envío</th>
        <th>Estado</th>
        <th>Actualizar</th>
      </tr>
      <?php while ($p = $pedidos->fetch_assoc()): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= $p['usuario'] ?></td>
          <td><?= $p['fecha'] ?></td>
          <td>Q<?= number_format($p['total'], 2) ?></td>
          <td><?= $p['correo'] ?></td>
          <td><?= $p['nit'] ?></td>
          <td><?= $p['metodo_pago'] ?></td>
          <td><?= $p['metodo_envio'] ?? 'Sin método' ?></td>
          <td><?= $p['estado_envio'] ?></td>
          <td>
            <form method="POST" action="actualizar_estado.php">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <select name="estado_envio">
                <option value="Pendiente" <?= $p['estado_envio'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="Preparando" <?= $p['estado_envio'] === 'Preparando' ? 'selected' : '' ?>>Preparando</option>
                <option value="Enviado" <?= $p['estado_envio'] === 'Enviado' ? 'selected' : '' ?>>Enviado</option>
                <option value="Entregado" <?= $p['estado_envio'] === 'Entregado' ? 'selected' : '' ?>>Entregado</option>
                <option value="Cancelado" <?= $p['estado_envio'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
              </select>
              <button type="submit">ENVIAR</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
