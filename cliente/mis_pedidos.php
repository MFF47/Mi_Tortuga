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
$pedidos = $conn->query("SELECT p.*, m.nombre AS envio 
                         FROM pedidos p 
                         LEFT JOIN metodos_envio m ON p.metodo_envio_id = m.id 
                         WHERE p.usuario = '$usuario' 
                         ORDER BY p.fecha ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Pedidos</title>
  <link rel="stylesheet" href="../css/estilos.css">
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f5f5f5; }
    ul.productos { margin: 8px 0 12px 0; padding-left: 20px; text-align: left; }
    ul.productos li { margin-bottom: 4px; list-style-type: disc; }
    .productos-cell { text-align: left; padding-left: 12px; background-color: #fafafa; }

    /* Colores por estado */
    .estado-pendiente { background-color: #ffeeba; }
    .estado-preparando { background-color: #cce5ff; }
    .estado-enviado { background-color: #d4edda; }
    .estado-entregado { background-color: #b8f0c2; }
    .estado-cancelado { background-color: #f8d7da; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>📦 Seguimiento de Pedidos</h2>
    <table>
      <tr>
        <th>#</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Envío</th>
        <th>Estado</th>
      </tr>
      <?php while ($p = $pedidos->fetch_assoc()): 
        // Determinar clase CSS según estado
        $estado = strtolower($p['estado_envio']);
        $clase_estado = match ($estado) {
          'pendiente'   => 'estado-pendiente',
          'preparando'  => 'estado-preparando',
          'enviado'     => 'estado-enviado',
          'entregado'   => 'estado-entregado',
          'cancelado'   => 'estado-cancelado',
          default       => ''
        };
      ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= $p['fecha'] ?></td>
          <td>Q<?= number_format($p['total'], 2) ?></td>
          <td><?= $p['envio'] ?? 'Sin método' ?></td>
          <td class="<?= $clase_estado ?>"><?= ucfirst($p['estado_envio']) ?></td>
        </tr>

        <!-- Productos comprados -->
        <tr>
          <td colspan="5" class="productos-cell">
            <strong>🛒 Productos comprados:</strong>
            <ul class="productos">
              <?php
              $detalle = $conn->query("SELECT dp.*, pr.nombre 
                                       FROM detalle_pedido dp 
                                       JOIN productos pr ON dp.producto_id = pr.id 
                                       WHERE dp.pedido_id = " . $p['id']);
              while ($d = $detalle->fetch_assoc()):
              ?>
                <li>
                  <?= htmlspecialchars($d['nombre']) ?> — <?= $d['cantidad'] ?> unidad(es) × Q<?= number_format($d['precio_unitario'], 2) ?>
                </li>
              <?php endwhile; ?>
            </ul>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
