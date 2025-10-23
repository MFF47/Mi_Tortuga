<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$id = intval($_GET['id']);
$producto = $conn->query("SELECT * FROM productos WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $conn->real_escape_string($_POST['nombre']);
  $precio = floatval($_POST['precio']);
  $stock = intval($_POST['stock']);
  $descripcion = $conn->real_escape_string($_POST['descripcion']);

  $sql = "UPDATE productos SET nombre = ?, precio = ?, stock = ?, descripcion = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sdisi", $nombre, $precio, $stock, $descripcion, $id);
  $stmt->execute();

  header("Location: admin_productos.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>✏️ Editar Producto</h2>
    <form method="POST">
      <label>Nombre:</label>
      <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>

      <label>Precio:</label>
      <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required>

      <label>Stock:</label>
      <input type="number" name="stock" value="<?= $producto['stock'] ?>" required>
<br>
<br>
      <label>Descripción:</label>
      <br>
      <br>
      <textarea name="descripcion" rows="4" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
<br>
      <button type="submit">Actualizar</button>
    </form>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
