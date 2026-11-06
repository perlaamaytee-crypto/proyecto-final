<?php
require "conexion.php";

$stmt = $conn->prepare(
  "INSERT INTO respuestas (nombre, carrera, profesores, instalaciones, biblioteca, cafeteria, recomendacion)
   VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssssss",
  $_POST['nombre'], $_POST['carrera'], $_POST['profesores'],
  $_POST['instalaciones'], $_POST['biblioteca'],
  $_POST['cafeteria'], $_POST['recomendacion']
);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: regalo.php");
exit;
?>
