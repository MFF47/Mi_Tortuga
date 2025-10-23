<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$rol = $_SESSION['rol'] ?? null;
?>

<header style="background:#0a2a5f; padding:15px;">
  <h1 style="color:white;">TecnoStore Guate</h1>
  <nav>
    <ul style="list-style:none; display:flex; gap:15px; padding:0; margin-top:10px;">
      <?php if ($rol === 'admin'): ?>
        <li><a href="dashboard.php" style="color:white;">Inicio</a></li>
        <li><a href="admin_productos.php" style="color:white;">Inventario</a></li>
        <li><a href="soporte.php" style="color:white;">Soporte</a></li>
        <li><a href="perfil.php" style="color:white;">Mi perfil</a></li>
        <li><a href="../logout.php" style="color:white;">Cerrar Sesión</a></li>
      <?php elseif ($rol === 'usuario'): ?>
        <li><a href="dashboard.php" style="color:white;">Productos</a></li>
        <li><a href="carrito.php" style="color:white;">Carrito</a></li>
        <li><a href="mis_pedidos.php" style="color:white;">Mis Pedidos</a></li>
        <li><a href="soporte.php" style="color:white;">Soporte</a></li>
         <li><a href="faq.php" style="color:white;">Preguntas Frecuentes</a></li>
        <li><a href="perfil.php" style="color:white;">Mi perfil</a></li>
        <li><a href="../logout.php" style="color:white;">Cerrar Sesión</a></li>
      <?php else: ?>
        <li><a href="../index.html" style="color:white;">Iniciar Sesión</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>
