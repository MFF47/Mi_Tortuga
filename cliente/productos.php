<?php
include '../db.php';

include '../nombres_paginas.php';

$archivo = basename($_SERVER['PHP_SELF']);
$pagina = $nombres_paginas[$archivo] ?? $archivo;

$usuario = $_SESSION['username'] ?? 'Invitado';
$conn->query("INSERT INTO visitas (usuario, pagina) VALUES ('$usuario', '$pagina')");


$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$precio_min = isset($_GET['precio_min']) ? $_GET['precio_min'] : '';
$precio_max = isset($_GET['precio_max']) ? $_GET['precio_max'] : '';


$condiciones = ["disponible = 1"];
if ($buscar !== '') {
  $condiciones[] = "nombre LIKE '%$buscar%'";
}
if ($categoria !== '') {
  $condiciones[] = "categoria = '$categoria'";
}
if ($precio_min !== '') {
  $condiciones[] = "precio >= $precio_min";
}
if ($precio_max !== '') {
  $condiciones[] = "precio <= $precio_max";
}

$where = implode(" AND ", $condiciones);
$sql = "SELECT * FROM productos WHERE $where ORDER BY id DESC";
$resultado = $conn->query($sql);
?>

<section class="productos">
  <form method="GET" class="filtros">
    <input type="text" name="buscar" placeholder="Buscar producto..." value="<?php echo $buscar; ?>">
    <select name="categoria">
      <option value="">Todas las categorías</option>
      <option value="Laptops" <?php if ($categoria == 'Laptops') echo 'selected'; ?>>Laptops</option>
      <option value="Smartphones" <?php if ($categoria == 'Smartphones') echo 'selected'; ?>>Smartphones</option>
      <option value="Accesorios" <?php if ($categoria == 'Accesorios') echo 'selected'; ?>>Accesorios</option>
    </select>
    <input type="number" name="precio_min" placeholder="Precio mínimo" value="<?php echo $precio_min; ?>">
    <input type="number" name="precio_max" placeholder="Precio máximo" value="<?php echo $precio_max; ?>">
    <button type="submit">Filtrar</button>
  </form>

  <div class="grid-productos">
    <?php if ($resultado->num_rows > 0): ?>
      <?php while ($row = $resultado->fetch_assoc()): ?>
        <div class="producto">
          <div class="imagen">
            <img src="../img/<?php echo $row['imagen'] ?: 'placeholder.png'; ?>" alt="Producto">
          </div>
          <h3><?php echo $row['nombre']; ?></h3>
          <p><?php echo $row['descripcion']; ?></p>
          <p><strong>Precio:</strong> Q<?php echo number_format($row['precio'], 2); ?></p>
          <p><strong>Disponibles:</strong> <?php echo $row['stock']; ?></p>
 <a href="agregar_carrito.php?id=<?php echo $row['id']; ?>">
  <button>Agregar al carrito</button>
</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No se encontraron productos con los filtros seleccionados.</p>
    <?php endif; ?>
  </div>
</section>
