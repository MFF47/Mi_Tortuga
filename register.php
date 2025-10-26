<?php
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];


$sql = "INSERT INTO usuarios (nombre, apellido, username, password, rol) VALUES ('$nombre', '$apellido', '$username', '$password', 'usuario')";
if ($conn->query($sql) === TRUE) {
  echo "<script>
          alert('Usuario creado exitosamente. Eres un crack.');
          window.location.href = 'index.html';
        </script>";
} else {
  echo "<script>
          alert('Error al crear el usuario: " . $conn->error . "');
          window.location.href = 'registro.html';
        </script>";
}
?>
