<?php
include 'db.php';



$username = $_POST['username'];
$password = $_POST['password'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$username = $_POST['username'];

$sql = "INSERT INTO usuarios (nombre, apellido, username, password, rol) VALUES ('$nombre', '$apellido', '$username', '$password', 'usuario')";
if ($conn->query($sql) === TRUE) {
  echo "Registro exitoso. Ahora puedes iniciar sesión.";
} else {
  echo "Error: " . $conn->error;
}
?>
