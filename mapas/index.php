<?php
// La clave de Google Maps NO va en el código: se lee de la variable MAPS_API_KEY de Render.
$maps_key = getenv('MAPS_API_KEY');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cómo llegar</title>
  <link rel="stylesheet" href="../estilos.css">
  <style>
    main { max-width: 800px; }
    .contenedor-mapa { margin-bottom: 30px; }
    #mapa-interactivo { height: 450px; width: 100%; border-radius: 6px; }
  </style>
</head>
<body>
  <header>
    <img src="../foto.png" alt="Mi foto">
    <h1>Ubicación</h1>
    <p>Perla Nava</p>
  </header>
  <main>
    <h2>Cómo llegar</h2>

    <!-- Etapa 1: mapa con iframe -->
    <div class="contenedor-mapa">
      <h3>Mapa con iframe</h3>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3765.98!2d-99.19!3d19.60!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTnvsDI2JzA3LjYiTiA5OcKwMTEnMzUuNSJX!5e0!3m2!1ses!2smx!4v123456789"
        width="100%"
        height="400"
        style="border:0;"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>

    <!-- Etapa 3: mapa interactivo con la API de JavaScript -->
    <div class="contenedor-mapa">
      <h3>Mapa interactivo</h3>
      <div id="mapa-interactivo"></div>
    </div>

    <a class="btn" href="../index.php">Volver al inicio</a>
  </main>

  <script>
    function initMap() {
      const ubicacion = { lat: 19.6021, lng: -99.1932 };
      const mapa = new google.maps.Map(document.getElementById("mapa-interactivo"), {
        zoom: 15,
        center: ubicacion,
      });
      new google.maps.Marker({
        position: ubicacion,
        map: mapa,
        title: "Ubicación Principal",
      });
    }
  </script>

  <?php if ($maps_key): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode($maps_key); ?>&callback=initMap" async defer></script>
  <?php else: ?>
    <script>document.getElementById("mapa-interactivo").innerHTML = "<p>Falta configurar la clave del mapa.</p>";</script>
  <?php endif; ?>
</body>
</html>
