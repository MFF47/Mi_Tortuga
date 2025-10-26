<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'usuario') {
  header("Location: ../index.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <link rel="stylesheet" href="../css/estilos.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <meta charset="UTF-8">
  <title>TecnoStore Guate - Bienvenido</title>
  <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main class="contenido">
    <h2>Bienvenido a TecnoStore Guate, <?php echo $_SESSION['username']; ?></h2>
    <p>Explora nuestros productos tecnológicos más recientes:</p>

    <?php include 'productos.php'; ?>
  </main>

  <?php include 'footer.php'; ?>
</body>
</html>
