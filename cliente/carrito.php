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
$sql = "SELECT c.id, p.nombre, p.precio, c.cantidad 
        FROM carrito c 
        JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario = '$usuario'";
$result = $conn->query($sql);

$subtotal = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Carrito - TecnoStore Guate</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>🛒 Tu Carrito</h2>

    <?php if ($result->num_rows > 0): ?>
      <form method="POST" action="actualizar_carrito.php">
        <table>
          <tr>
            <th>Producto</th>
            <th>Precio (Q)</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Acción</th>
          </tr>
          <?php while ($row = $result->fetch_assoc()): 
            $total_producto = $row['precio'] * $row['cantidad'];
            $subtotal += $total_producto;
          ?>
            <tr>
              <td><?= htmlspecialchars($row['nombre']) ?></td>
              <td>Q<?= number_format($row['precio'], 2) ?></td>
              <td>
                <input type="number" name="cantidades[<?= $row['id'] ?>]" value="<?= $row['cantidad'] ?>" min="1" style="width: 60px;">
              </td>
              <td>Q<?= number_format($total_producto, 2) ?></td>
              <td><a href="eliminar_carrito.php?id=<?= $row['id'] ?>">Eliminar</a></td>
            </tr>
          <?php endwhile; ?>
        </table>
        <button type="submit">Actualizar Cantidades</button>
      </form>

      <?php

        $precio_sin_iva = $subtotal / 1.12;
        $iva = $subtotal - $precio_sin_iva;
        $envio = 35.00;
        $total_final = $subtotal + $envio;
      ?>

      <div class="resumen">
        <p><strong>Subtotal sin IVA:</strong> Q<?= number_format($precio_sin_iva, 2) ?></p>
        <p><strong>IVA (12% incluido):</strong> Q<?= number_format($iva, 2) ?></p>
        <p><strong>Envío:</strong> Q<?= number_format($envio, 2) ?></p>
        <hr>
        <p><strong>Total a pagar:</strong> Q<?= number_format($total_final, 2) ?></p>

        <form method="GET" action="pago.php">
          <button type="submit">Proceder al Pago</button>
        </form>
      </div>
    <?php else: ?>
      <p>No tienes productos en el carrito.</p>
    <?php endif; ?>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
