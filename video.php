<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi video</title>
  <link rel="stylesheet" href="estilos.css">
  <style>
    .video-box { position: relative; width: 100%; max-width: 360px; margin: 0 auto;
                 padding-bottom: 177.78%; height: 0; overflow: hidden; border-radius: 10px; }
    .video-box iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
  </style>
</head>
<body>
  <header>
    <img src="foto.png" alt="Mi foto">
    <h1>Mi video</h1>
  </header>
  <main style="text-align:center">
    <div class="video-box">
      <iframe src="https://www.youtube.com/embed/o-uov-LlTBI" title="Mi video" allowfullscreen></iframe>
    </div>
    <a class="btn" href="index.php">Volver al inicio</a>
  </main>
</body>
</html>
