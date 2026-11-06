<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>¡Gracias!</title>
  <link rel="stylesheet" href="estilos.css">
  <style>
    .caja { font-size: 110px; cursor: pointer; display: inline-block; animation: bote 1s infinite; }
    .caja:hover { transform: scale(1.1); }
    @keyframes bote { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
    #premio { font-size: 90px; display: none; animation: aparece 0.6s; }
    #mensaje { font-size: 18px; margin-top: 10px; }
    @keyframes aparece { from { transform: scale(0) rotate(-180deg); } to { transform: scale(1) rotate(0); } }
  </style>
</head>
<body>
  <header>
    <img src="foto.png" alt="Mi foto">
    <h1>¡Gracias por participar!</h1>
  </header>
  <main style="text-align:center">
    <h2>🎁 Tu regalo te espera</h2>
    <p id="instruccion">Toca la caja para abrirla</p>
    <div class="caja" id="caja" onclick="abrir()">🎁</div>
    <div id="premio"></div>
    <p id="mensaje"></p>
    <a class="btn" href="index.php">Volver al inicio</a>
  </main>

  <script>
    const regalos = [
      { emoji: "🌮", texto: "¡Te ganaste un taco virtual! Sin calorías, sin salsa y sin taco." },
      { emoji: "🦆", texto: "¡Un patito de hule! No sirve para nada, pero te escuchará todos tus bugs." },
      { emoji: "🤗", texto: "¡Un abrazo virtual! Válido en cualquier momento, no incluye contacto físico." },
      { emoji: "🏆", texto: "¡Trofeo al Mejor Encuestado del Año! Solo existe en tu imaginación." },
      { emoji: "🍕", texto: "¡Una pizza de pixeles! Calorías: 0. Sabor: 0. Emoción: 100." },
      { emoji: "😴", texto: "¡Un pase para dormir en clase! Caduca cuando llegue el profe." }
    ];

    function abrir() {
      const r = regalos[Math.floor(Math.random() * regalos.length)];
      document.getElementById("caja").style.display = "none";
      document.getElementById("instruccion").style.display = "none";
      const premio = document.getElementById("premio");
      premio.textContent = r.emoji;
      premio.style.display = "block";
      document.getElementById("mensaje").textContent = r.texto;
    }
  </script>
</body>
</html>
