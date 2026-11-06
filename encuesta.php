<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulario</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <header>
    <img src="foto.png" alt="Mi foto">
    <h1>Encuesta UPVM</h1>
  </header>
  <main>
    <form action="guardar.php" method="POST">
      <label>Nombre:</label>
      <input type="text" name="nombre" required>

      <label>Carrera:</label>
      <select name="carrera" required>
        <option value="">-- Selecciona --</option>
        <option>Ingeniería en Alimentos</option>
        <option>Ingeniería Agroindustrial</option>
        <option>Ingeniería en Biotecnología</option>
        <option>Ingeniería en Tecnologías de la Información e Innovación Digital</option>
        <option>Ingeniería en Logística</option>
        <option>Ingeniería Mecánica Automotriz</option>
        <option>Ingeniería Mecatrónica</option>
        <option>Ingeniería en Nanotecnología</option>
        <option>Ingeniería Industrial</option>
        <option>Licenciatura en Administración</option>
        <option>Licenciatura en Enfermería</option>
        <option>Licenciatura en Terapia Física</option>
        <option>Otra (sobreviviendo)</option>
      </select>

      <label>1. ¿Qué tal los profesores?</label>
      <select name="profesores" required>
        <option value="">-- Selecciona --</option>
        <option>Unos genios, hasta me caen bien</option>
        <option>Buenos, dan puntos extra (a veces)</option>
        <option>Regulares, como mi WiFi</option>
        <option>Mejor no opino, capaz me escuchan</option>
      </select>

      <label>2. ¿Cómo ves las instalaciones (salones, laboratorios)?</label>
      <select name="instalaciones" required>
        <option value="">-- Selecciona --</option>
        <option>Un hotel de 5 estrellas (casi)</option>
        <option>Bien, solo les falta Netflix</option>
        <option>Regulares, el aire es el viento</option>
        <option>Cada día es una aventura nueva</option>
      </select>

      <label>3. ¿Y la biblioteca?</label>
      <select name="biblioteca" required>
        <option value="">-- Selecciona --</option>
        <option>Silencio total, hasta mi estómago se calla</option>
        <option>Excelente para siestas culturales</option>
        <option>Regular, nunca encuentro el libro</option>
        <option>Solo voy por el clima</option>
      </select>

      <label>4. ¿Cómo está la cafetería?</label>
      <select name="cafeteria" required>
        <option value="">-- Selecciona --</option>
        <option>Tan rica que olvido la dieta</option>
        <option>Buena, mi cartera llora pero feliz</option>
        <option>Regular, la fila es eterna</option>
        <option>Mejor traigo mi lonche</option>
      </select>

      <label>5. ¿Recomendarías la UPVM?</label>
      <select name="recomendacion" required>
        <option value="">-- Selecciona --</option>
        <option>¡Claro, hasta les hago publicidad!</option>
        <option>Depende de si hay examen mañana</option>
        <option>Solo si me invitan un café</option>
        <option>Lo pensaré después de entregar tareas</option>
      </select>

      <button class="btn" type="submit">Enviar</button>
    </form>
  </main>
</body>
</html>
