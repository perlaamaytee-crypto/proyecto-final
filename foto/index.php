<?php
// foto/index.php

require 'sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir foto</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <main class="tarjeta">
        <h1>Subir foto</h1>
        <p>Sesión: <strong><?= e($_SESSION['nombre']) ?></strong></p>

        <!-- Opción 1: cámara en vivo -->
        <video id="video" autoplay playsinline muted hidden></video>
        <div class="botones">
            <button type="button" id="btn-camara">Activar cámara</button>
            <button type="button" id="btn-tomar" disabled>Tomar foto</button>
        </div>

        <!-- Opción 2: elegir un archivo del equipo -->
        <label>o elige una foto de tu equipo
            <input type="file" id="archivo" accept="image/*">
        </label>

        <img id="vista-previa" alt="Vista previa de la foto" hidden>
        <button type="button" id="btn-subir" disabled>Subir foto</button>
        <p id="mensaje" hidden></p>

        <h2>Galería</h2>
        <div id="galeria" class="galeria"></div>
        <p><a href="../index.php">← Volver al menú</a></p>
    </main>
    <script src="foto.js"></script>
</body>
</html>
