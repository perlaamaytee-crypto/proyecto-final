<?php
// Dirección del chat. Se pone en Render como variable CHAT_URL.
$chat_url = getenv('CHAT_URL');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proyecto final</title>
  <link rel="stylesheet" href="estilos.css">
  <style>
    .menu a { display: block; margin: 12px 0; text-align: center; }
  </style>
</head>
<body>
  <header>
    <img src="foto.png" alt="Mi foto">
    <h1>Proyecto final</h1>
    <p>Perla Nava</p>
  </header>
  <main class="menu">
    <h2>¿A dónde quieres ir?</h2>
    <a class="btn" href="encuesta.php">📝 Encuesta UPVM</a>
    <a class="btn" href="resultados.php">📊 Resultados de la encuesta</a>
    <a class="btn" href="video.php">🎬 Mi video</a>
    <a class="btn" href="crud/index.php">🗂️ CRUD de productos</a>
    <a class="btn" href="login/index.php">🔐 Login</a>
    <a class="btn" href="seguir/index.php">📈 Seguir (métricas)</a>
    <a class="btn" href="ubicacion/index.php">📍 Ubicación</a>
    <a class="btn" href="foto/index.php">📷 Foto</a>
    <?php if ($chat_url): ?>
      <a class="btn" href="<?php echo htmlspecialchars($chat_url); ?>">💬 Chat</a>
    <?php else: ?>
      <p style="text-align:center">💬 El chat todavía no está publicado.</p>
    <?php endif; ?>
    <a class="btn" href="mapas/index.php">🗺️ Cómo llegar</a>
  </main>
</body>
</html>
