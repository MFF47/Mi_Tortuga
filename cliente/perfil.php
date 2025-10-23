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
$datos = $conn->query("SELECT nombre, apellido FROM usuarios WHERE username = '$usuario'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil de Usuario - TecnoStore Guate</title>
  <link rel="stylesheet" href="../css/estilos.css">
  <style>
    .perfil-box {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .perfil-box h2 {
      text-align: center;
      color: #007bff;
      margin-bottom: 20px;
    }

    .perfil-box label {
      display: block;
      margin-top: 10px;
      font-weight: 500;
    }

    .perfil-box input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .perfil-box button {
      margin-top: 20px;
      width: 100%;
      padding: 10px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    .perfil-box button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <main class="contenido">
    <div class="perfil-box">
      <h2>👤 Perfil de Usuario</h2>
      <form method="POST" action="actualizar_perfil.php">
         <label>Usuario:</label>
        <input type="text" name="nuevo_username" value="<?php echo $usuario; ?>" required>

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $datos['nombre']; ?>" required>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?php echo $datos['apellido']; ?>" required>

        <label>Nueva Contraseña (opcional):</label>
        <input type="password" name="nueva_password" placeholder="Solo si deseas cambiarla">

        <button type="submit">Actualizar Perfil</button>
      </form>
    </div>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
