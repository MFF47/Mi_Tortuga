<?php
include '../db.php';
$id = intval($_GET['id']);
$conn->query("DELETE FROM reseñas WHERE id = $id");
header("Location: admin_reseñas.php");
