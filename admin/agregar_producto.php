<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $conn->real_escape_string($_POST['nombre']);
  $precio = floatval($_POST['precio']);
  $stock = intval($_POST['stock']);
  $descripcion = $conn->real_escape_string($_POST['descripcion']);
  $categoria = $conn->real_escape_string($_POST['categoria']);

  $imagen = null;
  if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $nombreArchivo = basename($_FILES['imagen']['name']);
    $rutaDestino = '../img/productos/' . $nombreArchivo;

    if (!is_dir('../img/productos')) {
      mkdir('../img/productos', 0777, true);
    }

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
      $imagen = $nombreArchivo;
    }
  }

  $sql = "INSERT INTO productos (nombre, precio, stock, descripcion, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sdisss", $nombre, $precio, $stock, $descripcion, $categoria, $imagen);
  $stmt->execute();

  header("Location: admin_productos.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Producto</title>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <h2>➕ Nuevo Producto</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Nombre:</label>
      <input type="text" name="nombre" required>

      <label>Precio:</label>
      <input type="number" step="0.01" name="precio" required>

      <label>Stock:</label>
      <input type="number" name="stock" required>
<br>
<br>
      <label>Descripción:</label>
      <br>
      <textarea name="descripcion" rows="4" placeholder="Describe el producto..." required></textarea>
<br>
<br>
      <label>Categoría:</label>
      <select name="categoria" required>
        <option value="Laptops">Laptops</option>
        <option value="Smartphones">Smartphones</option>
        <option value="Accesorios">Accesorios</option>
      </select>

      <br>
      <br>
      <label>Imagen del producto:</label>
      <input type="file" name="imagen" accept="image/*" required>

      <br>
      <br>
      <button type="submit">Guardar</button>
    </form>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
