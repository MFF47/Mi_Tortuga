<?php
session_start();
include '../db.php';
include '../nombres_paginas.php';
include '../factura_html.php'; // ✅ Usamos la función reutilizable

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use Dompdf\Dompdf;
require '../vendor/autoload.php';


$archivo = basename($_SERVER['PHP_SELF']);
$pagina = $nombres_paginas[$archivo] ?? $archivo;
$usuario = $_SESSION['username'] ?? 'Invitado';
$conn->query("INSERT INTO visitas (usuario, pagina) VALUES ('$usuario', '$pagina')");


if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$usuario = $_SESSION['username'];
$correo = $_POST['correo'];
$nit = $_POST['nit'];
$metodo = $_POST['metodo_pago'];
$metodo_envio_id = intval($_POST['metodo_envio']);
$comentario = isset($_POST['comentario']) ? $conn->real_escape_string($_POST['comentario']) : '';
$valoracion = isset($_POST['valoracion']) ? intval($_POST['valoracion']) : 5;


$envio_data = $conn->query("SELECT * FROM metodos_envio WHERE id = $metodo_envio_id")->fetch_assoc();
$costo_envio = $envio_data ? floatval($envio_data['base']) : 35.00;

$sql = "SELECT c.producto_id, c.cantidad, p.precio 
        FROM carrito c 
        JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "<p>Tu carrito está vacío.</p>";
  exit();
}


$total = 0;
$items = [];
while ($row = $result->fetch_assoc()) {
  $subtotal = $row['precio'] * $row['cantidad'];
  $total += $subtotal;
  $items[] = $row;
}

$precio_sin_iva = $total / 1.12;
$iva = $total - $precio_sin_iva;
$envio = $costo_envio;
$total_final = $total + $envio;


$sql_pedido = "INSERT INTO pedidos (usuario, total, metodo_envio_id, correo, nit, metodo_pago) VALUES (?, ?, ?, ?, ?, ?)";
$stmt_pedido = $conn->prepare($sql_pedido);
$stmt_pedido->bind_param("sdisss", $usuario, $total_final, $metodo_envio_id, $correo, $nit, $metodo);
$stmt_pedido->execute();
$pedido_id = $stmt_pedido->insert_id;

$sql_detalle = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
$stmt_detalle = $conn->prepare($sql_detalle);
foreach ($items as $item) {
  $producto_id = $item['producto_id'];
  $cantidad = $item['cantidad'];
  $precio = $item['precio'];

  $stmt_detalle->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $precio);
  $stmt_detalle->execute();

  $stmt_stock = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
  $stmt_stock->bind_param("ii", $cantidad, $producto_id);
  $stmt_stock->execute();
}


$conn->query("DELETE FROM carrito WHERE usuario = '$usuario'");


if (!empty($comentario)) {
  $sql_reseña = "INSERT INTO reseñas (usuario, producto_id, comentario, valoracion) VALUES (?, 0, ?, ?)";
  $stmt_reseña = $conn->prepare($sql_reseña);
  $stmt_reseña->bind_param("ssi", $usuario, $comentario, $valoracion);
  $stmt_reseña->execute();
}


$html = generarFacturaHTML($pedido_id, $conn);


$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdf_output = $dompdf->output();
$pdf_path = "../facturas/factura_$pedido_id.pdf";
file_put_contents($pdf_path, $pdf_output);


$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'mff4745@gmail.com';
  $mail->Password = 'aubzarbeazqeskzb';
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;

  $mail->setFrom('mff4745@gmail.com', 'TecnoStore Guate');
  $mail->addAddress($correo, $usuario);
  $mail->Subject = 'Tu factura de TecnoStore Guate';
  $mail->Body = "Hola $usuario,\n\nGracias por tu compra. Adjuntamos tu factura en PDF.\n\nSaludos,\nTecnoStore Guate";
  $mail->addAttachment($pdf_path);

  $mail->send();
} catch (Exception $e) {
  error_log("Error al enviar factura: " . $mail->ErrorInfo);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pedido Confirmado</title>
  <link rel="stylesheet" href="../css/estilos.css">
  <style>
    .resumen { max-width: 600px; margin: auto; text-align: center; padding: 30px; }
    .resumen button { padding: 10px 20px; margin: 10px; font-size: 16px; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="resumen">
    <h2 style="color:#198754;">✅ Pedido confirmado</h2>
    <p>Gracias por tu compra, <strong><?= htmlspecialchars($usuario) ?></strong>.</p>
    <p>Tu pedido ha sido registrado correctamente.</p>

    <a href="generar_factura.php?id=<?= $pedido_id ?>" target="_blank">
      <button>📄 Descargar Factura PDF</button>
    </a>

    <a href="mis_pedidos.php">
      <button>📦 Ir a Mis Pedidos</button>
    </a>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
