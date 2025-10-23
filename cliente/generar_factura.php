<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;

session_start();
include '../db.php';

if (!isset($_GET['id'])) {
  echo "Factura no disponible.";
  exit();
}

$pedido_id = intval($_GET['id']);

// Obtener datos del pedido
$sql = "SELECT p.*, m.nombre AS metodo_envio, m.tiempo_estimado 
        FROM pedidos p 
        JOIN metodos_envio m ON p.metodo_envio_id = m.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();

if (!$pedido) {
  echo "Pedido no encontrado.";
  exit();
}

// Obtener detalle del pedido
$sql_detalle = "SELECT dp.*, pr.nombre 
                FROM detalle_pedido dp 
                JOIN productos pr ON dp.producto_id = pr.id 
                WHERE dp.pedido_id = ?";
$stmt = $conn->prepare($sql_detalle);
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$detalles = $stmt->get_result();

// Construir HTML de la factura
ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; }
    .factura { max-width: 800px; margin: auto; padding: 20px; }
    h2 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f5f5f5; }
  </style>
</head>
<body>
  <div class="factura">
    <h2>Factura Digital</h2>
    <p><strong>Factura No.:</strong> <?= $pedido_id ?></p>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['usuario']) ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($pedido['correo']) ?></p>
    <p><strong>NIT:</strong> <?= htmlspecialchars($pedido['nit']) ?></p>
    <p><strong>Método de pago:</strong> <?= htmlspecialchars($pedido['metodo_pago']) ?></p>
    <p><strong>Método de envío:</strong> <?= htmlspecialchars($pedido['metodo_envio']) ?> (<?= $pedido['tiempo_estimado'] ?>)</p>

    <table>
      <tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr>
      <?php
      $subtotal = 0;
      while ($d = $detalles->fetch_assoc()):
        $linea = $d['cantidad'] * $d['precio_unitario'];
        $subtotal += $linea;
      ?>
        <tr>
          <td><?= $d['nombre'] ?></td>
          <td><?= $d['cantidad'] ?></td>
          <td>Q<?= number_format($d['precio_unitario'], 2) ?></td>
          <td>Q<?= number_format($linea, 2) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <?php
    $precio_sin_iva = $subtotal / 1.12;
    $iva = $subtotal - $precio_sin_iva;
    $envio = $pedido['total'] - $subtotal;
    ?>

    <p><strong>Subtotal sin IVA:</strong> Q<?= number_format($precio_sin_iva, 2) ?></p>
    <p><strong>IVA (12%):</strong> Q<?= number_format($iva, 2) ?></p>
    <p><strong>Envío:</strong> Q<?= number_format($envio, 2) ?></p>
    <hr>
    <p><strong>Total pagado:</strong> Q<?= number_format($pedido['total'], 2) ?></p>
  </div>
</body>
</html>

<?php
$html = ob_get_clean();

// Generar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Factura_$pedido_id.pdf", ["Attachment" => false]);
exit();
