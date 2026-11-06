<?php require "conexion.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultados</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <header>
    <img src="foto.png" alt="Mi foto">
    <h1>Respuestas de la encuesta</h1>
  </header>
  <main style="overflow-x:auto">
    <table border="1" cellpadding="6" style="width:100%; border-collapse:collapse">
      <tr><th>ID</th><th>Nombre</th><th>Carrera</th><th>Profesores</th></tr>
      <?php
      $res = $conn->query("SELECT id, nombre, carrera, profesores FROM respuestas ORDER BY id DESC");
      while ($fila = $res->fetch_assoc()) {
        echo "<tr><td>" . (int)$fila['id'] . "</td><td>" . htmlspecialchars($fila['nombre']) .
             "</td><td>" . htmlspecialchars($fila['carrera']) .
             "</td><td>" . htmlspecialchars($fila['profesores']) . "</td></tr>";
      }
      ?>
    </table>
    <a class="btn" href="index.php">Volver al inicio</a>
  </main>
</body>
</html>
