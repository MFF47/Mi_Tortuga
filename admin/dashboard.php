<?php
session_start();
include '../db.php';
include 'header.php';



if ($_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
  
<head>
  <meta charset="UTF-8">
  <title>Panel Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
    margin: 0;
    color: #333;
  }
</style>

</head>
<body>
  
<main class="admin-dashboard">
  
 <section class="bienvenida">
  <h2 class="titulo-admin">Bienvenido, <?php echo $_SESSION['username']; ?></h2>
  <p class="subtitulo-admin">Gestiona el sistema desde este panel central.</p>
</section>


  <section class="tarjetas">
    <div class="tarjeta">
      <h3>📦 Inventario</h3>
      <p>Controla productos, precios y stock.</p>
      <a href="admin_productos.php" class="boton">Ir al módulo</a>
    </div>
    <div class="tarjeta">
      <h3>🛠️ Soporte</h3>
      <p>Responde solicitudes y mensajes de clientes.</p>
      <a href="admin_reseñas.php" class="boton">Ver soporte</a>
    </div>
    <div class="tarjeta">
      <h3>👤 Usuarios</h3>
      <p>Administra roles y perfiles registrados.</p>
      <a href="admin_usuarios.php" class="boton">Gestionar usuarios</a>
    </div>
    <div class="tarjeta">
      <h3>📦 Pedidos</h3>
      <p>Administra pedidos de clientes.</p>
      <a href="admin_pedidos.php" class="boton">Ver pedidos</a>
    </div>
    <div class="tarjeta">
     <h3> 💬 Preguntas Frecuentes (FAQ)</h3>
      <p>Seccion para responder dudas.</p>
      <a href="admin_faq.php" class="boton">Preguntas frecuentes</a>
    </div>
     <div class="tarjeta">
     <h3> 📊 Analítica y Reportes (FAQ)</h3>
      <p>Seccion de reporteria y analisis</p>
      <a href="admin_analitica.php" class="boton">Reportes y analisis</a>
    </div>
  </section>
</main>

  <?php include 'footer.php'; ?>
</body>
</html>

  
</body>
</html>
