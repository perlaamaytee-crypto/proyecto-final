<?php
// login/bienvenida.php
// Página protegida: solo se ve si ya iniciaste sesión.

require 'conexion.php';

// Si no hay sesión, regresamos al login.
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT nombre, correo, sexo, edad, carrera FROM usuarios WHERE id = ?');
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenida</title>
  <link rel="stylesheet" href="../estilos.css">
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <header>
    <img src="../foto.png" alt="Mi foto">
    <h1>¡Hola, <?= e($usuario['nombre']) ?>!</h1>
    <p>Iniciaste sesión correctamente</p>
  </header>
  <main>
    <ul class="datos">
      <li><strong>Correo:</strong> <?= e($usuario['correo']) ?></li>
      <li><strong>Sexo:</strong> <?= e($usuario['sexo']) ?></li>
      <li><strong>Edad:</strong> <?= e($usuario['edad']) ?></li>
      <li><strong>Carrera:</strong> <?= e($usuario['carrera']) ?></li>
    </ul>
    <a class="btn" href="logout.php">Cerrar sesión</a>
    <a class="btn" href="../index.php">Volver al menú</a>
  </main>
</body>
</html>
