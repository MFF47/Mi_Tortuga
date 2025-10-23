<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

// Ventas por día
$ventas_dia = $conn->query("
  SELECT DATE(fecha) AS dia, SUM(total) AS ingresos
  FROM pedidos
  GROUP BY dia
  ORDER BY dia DESC
");

// Productos más vendidos
$productos = $conn->query("
  SELECT p.nombre, SUM(dp.cantidad) AS vendidos
  FROM detalle_pedido dp
  JOIN productos p ON dp.producto_id = p.id
  GROUP BY p.id
  ORDER BY vendidos DESC
");

// Ingresos por método de pago
$pagos = $conn->query("
  SELECT IFNULL(metodo_pago, 'Sin especificar') AS metodo, SUM(total) AS ingresos
  FROM pedidos
  GROUP BY metodo
");

// Visitas por página
$visitas = $conn->query("
  SELECT pagina, COUNT(*) AS total
  FROM visitas
  GROUP BY pagina
");

// Tasa de conversión
$total_visitas = $conn->query("SELECT COUNT(*) AS total FROM visitas")->fetch_assoc()['total'];
$total_pedidos = $conn->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc()['total'];
$tasa_conversion = $total_visitas > 0 ? round(($total_pedidos / $total_visitas) * 100, 2) : 0;

// Horarios de actividad
$horas = $conn->query("
  SELECT HOUR(fecha) AS hora, COUNT(*) AS visitas
  FROM visitas
  GROUP BY hora
  ORDER BY hora
");

// Clientes frecuentes
$clientes = $conn->query("
  SELECT usuario, COUNT(*) AS pedidos
  FROM pedidos
  GROUP BY usuario
  ORDER BY pedidos DESC
");

// Valor promedio por pedido
$promedio = $conn->query("SELECT AVG(total) AS promedio FROM pedidos")->fetch_assoc()['promedio'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📊 Analítica y Reportes</title>
  <link rel="stylesheet" href="../css/estilos.css">
  <style>
    .panel { max-width: 900px; margin: auto; padding: 20px; }
    .panel h3 { margin-top: 30px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f5f5f5; }
    ul { list-style: none; padding-left: 0; }
    li { margin-bottom: 5px; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="panel">
    <h2>📊 Analítica y Reportes</h2>
<div style="text-align:right; margin-bottom:10px;">
  <a href="descargar_analitica.php">
    <button style="padding:8px 16px;">📥 Descargar Excel</button>
  </a>
</div>

    <h3>🧾 Ventas por Día</h3>
    <table>
      <tr><th>Fecha</th><th>Ingresos</th></tr>
      <?php while ($v = $ventas_dia->fetch_assoc()): ?>
        <tr>
          <td><?= $v['dia'] ?></td>
          <td>Q<?= number_format($v['ingresos'], 2) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <h3>🔥 Productos Más Vendidos</h3>
    <table>
      <tr><th>Producto</th><th>Unidades Vendidas</th></tr>
      <?php while ($p = $productos->fetch_assoc()): ?>
        <tr>
          <td><?= $p['nombre'] ?></td>
          <td><?= $p['vendidos'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <h3>💳 Ingresos por Método de Pago</h3>
    <table>
      <tr><th>Método de Pago</th><th>Ingresos</th></tr>
      <?php while ($p = $pagos->fetch_assoc()): ?>
        <tr>
          <td><?= $p['metodo'] ?></td>
          <td>Q<?= number_format($p['ingresos'], 2) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <h3>📈 Tráfico del Sitio</h3>
    <table>
      <tr><th>Página</th><th>Visitas</th></tr>
      <?php while ($v = $visitas->fetch_assoc()): ?>
        <tr>
          <td><?= $v['pagina'] ?></td>
          <td><?= $v['total'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
    <p><strong>Tasa de conversión:</strong> <?= $tasa_conversion ?>%</p>

    <h3>🕒 Horarios de Mayor Actividad</h3>
    <table>
      <tr><th>Hora</th><th>Visitas</th></tr>
      <?php while ($h = $horas->fetch_assoc()): ?>
        <tr>
          <td><?= $h['hora'] ?>:00</td>
          <td><?= $h['visitas'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <h3>👥 Comportamiento del Cliente</h3>
    <table>
      <tr><th>Usuario</th><th>Pedidos Realizados</th></tr>
      <?php while ($c = $clientes->fetch_assoc()): ?>
        <tr>
          <td><?= $c['usuario'] ?></td>
          <td><?= $c['pedidos'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
    <p><strong>Valor promedio por pedido:</strong> Q<?= number_format($promedio, 2) ?></p>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
