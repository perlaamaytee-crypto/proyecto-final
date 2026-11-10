<?php
// ubicacion/index.php

require 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi ubicación</title>
    <!-- Leaflet: la librería que dibuja el mapa de OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <main class="tarjeta">
        <h1>Mi ubicación</h1>
        <p>Sesión: <strong><?= e($_SESSION['nombre']) ?></strong></p>

        <button type="button" id="btn-capturar">Capturar mi ubicación</button>
        <p id="mensaje" hidden></p>
        <p id="datos" hidden></p>

        <div id="mapa"></div>

        <h2>Últimas ubicaciones</h2>
        <ol id="historial" class="historial"></ol>
        <p><a href="../index.php">← Volver al menú</a></p>
    </main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="ubicacion.js"></script>
</body>
</html>
