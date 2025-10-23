<?php
session_start();
include '../db.php';

// Validar sesión y parámetros
if (!isset($_SESSION['username'])) {
  header("Location: ../index.html");
  exit();
}

$usuario = $_SESSION['username'];
$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($producto_id === 0) {
  echo "<p>Producto no válido.</p>";
  exit();
}

// Obtener datos del producto
$producto = $conn->query("SELECT * FROM productos WHERE id = $producto_id")->fetch_assoc();
if (!$producto) {
  echo "<p>Producto no encontrado.</p>";
  exit();
}

// Verificar si el usuario compró este producto
$comprado = false;
$check = $conn->query("SELECT * FROM detalle_pedido dp 
                      JOIN pedidos p ON dp.pedido_id = p.id 
                      WHERE p.usuario = '$usuario' AND dp.producto_id = $producto_id");
if ($check->num_rows > 0) {
  $comprado = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo $producto['nombre']; ?> - TecnoStore Guate</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2><?php echo $producto['nombre']; ?></h2>
    <p><?php echo $producto['descripcion']; ?></p>
    <p><strong>Precio:</strong> Q<?php echo number_format($producto['precio'], 2); ?></p>
    <img src="../img/<?php echo $producto['imagen']; ?>" alt="Imagen del producto" style="max-width:300px;">

    <!-- Mostrar reseñas aprobadas -->
    <section class="reseñas">
      <h3>🗣 Opiniones de clientes</h3>
      <?php
      $reseñas = $conn->query("SELECT * FROM reseñas WHERE producto_id = $producto_id AND aprobado = 1 ORDER BY fecha DESC");

      if ($reseñas->num_rows > 0) {
        while ($r = $reseñas->fetch_assoc()) {
          echo "<div class='reseña'>";
          echo "<p><strong>{$r['usuario']}</strong> ({$r['valoracion']}⭐)</p>";
          echo "<p>{$r['comentario']}</p>";
          echo "<hr>";
          echo "</div>";
        }
      } else {
        echo "<p>No hay reseñas aún para este producto.</p>";
      }
      ?>
    </section>

    <!-- Formulario para dejar reseña -->
    <?php if ($comprado): ?>
      <section class="formulario-reseña">
        <h3>📝 Deja tu reseña</h3>
        <form method="POST" action="guardar_reseña.php">
          <input type="hidden" name="producto_id" value="<?php echo $producto_id; ?>">
          
          <label>Valoración (1 a 5):</label>
          <select name="valoracion" required>
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="2">⭐⭐</option>
            <option value="1">⭐</option>
          </select>

          <label>Comentario:</label>
          <textarea name="comentario" required></textarea>

          <button type="submit">Enviar reseña</button>
        </form>
      </section>
    <?php endif; ?>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
