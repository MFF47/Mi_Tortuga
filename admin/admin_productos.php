<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

// Obtener todos los productos
$productos = $conn->query("SELECT * FROM productos ORDER BY id ASC");

// Detectar productos con stock bajo
$stock_bajo = $conn->query("SELECT * FROM productos WHERE stock <= 3 ORDER BY stock ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Productos - Admin</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>🛠️ Gestión de Productos</h2>
    <a href="agregar_producto.php"><button>➕ Agregar Producto</button></a>

    <?php if ($stock_bajo->num_rows > 0): ?>
      <div style="background:#fff3cd; border:1px solid #ffeeba; padding:15px; margin:20px 0;">
        <strong>⚠️ Alerta de stock bajo:</strong>
        <ul>
          <?php while ($p = $stock_bajo->fetch_assoc()): ?>
            <li>
              <?= htmlspecialchars($p['nombre']) ?> — <?= $p['stock'] ?> unidades disponibles
              <a href="editar_producto.php?id=<?= $p['id'] ?>">[Reabastecer]</a>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
    <?php endif; ?>

    <table border="1" cellpadding="8" style="margin-top:20px; width:100%; text-align:left;">
      <tr>
        <th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th>
      </tr>
      <?php while ($p = $productos->fetch_assoc()): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['nombre']) ?></td>
          <td>Q<?= number_format($p['precio'], 2) ?></td>
          <td><?= $p['stock'] ?></td>
          <td>
            <a href="editar_producto.php?id=<?= $p['id'] ?>">✏️ Editar</a> |
            <a href="eliminar_producto.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Eliminar este producto?')">🗑️ Eliminar</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
