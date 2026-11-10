<?php
// login/registro.php
// Formulario de registro y la lógica que valida los datos y crea la cuenta.

require 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $correo    = trim($_POST['correo'] ?? '');
    $sexo      = $_POST['sexo'] ?? '';
    $edad      = (int) ($_POST['edad'] ?? 0);
    $carrera   = trim($_POST['carrera'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    // Validaciones del lado del servidor (las del navegador se pueden saltar).
    if ($nombre === '' || $carrera === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)
        || !in_array($sexo, ['Hombre', 'Mujer', 'Otro'], true) || $edad < 15 || $edad > 99) {
        $error = 'Revisa que todos los datos sean válidos.';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Nunca se guarda la contraseña: solo su hash.
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO usuarios (nombre, correo, sexo, edad, carrera, password_hash) VALUES (?, ?, ?, ?, ?, ?)';
        try {
            $pdo->prepare($sql)->execute([$nombre, $correo, $sexo, $edad, $carrera, $hash]);
            header('Location: login.php?registrado=1');
            exit;
        } catch (PDOException $ex) {
            // 23000 = el correo ya existe (la columna es UNIQUE).
            if ($ex->getCode() !== '23000') {
                throw $ex;
            }
            $error = 'Ese correo ya está registrado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
  <link rel="stylesheet" href="../estilos.css">
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <header>
    <img src="../foto.png" alt="Mi foto">
    <h1>Crear cuenta</h1>
    <p>Perla Nava</p>
  </header>
  <main>
    <?php if ($error): ?>
      <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <form id="form-registro" method="post">
      <label>Nombre
        <input type="text" name="nombre" maxlength="100" required value="<?= e($_POST['nombre'] ?? '') ?>">
      </label>
      <label>Correo
        <input type="email" name="correo" maxlength="150" required value="<?= e($_POST['correo'] ?? '') ?>">
      </label>
      <label>Sexo
        <select name="sexo" required>
          <option value="">Selecciona…</option>
          <?php foreach (['Hombre', 'Mujer', 'Otro'] as $opcion): ?>
            <option <?= ($_POST['sexo'] ?? '') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Edad
        <input type="number" name="edad" min="15" max="99" required value="<?= e($_POST['edad'] ?? '') ?>">
      </label>
      <label>Carrera
        <input type="text" name="carrera" maxlength="100" required value="<?= e($_POST['carrera'] ?? '') ?>">
      </label>
      <label>Contraseña
        <input type="password" name="password" minlength="8" required>
      </label>
      <label>Confirmar contraseña
        <input type="password" name="confirmar" minlength="8" required>
      </label>
      <button class="btn" type="submit">Registrarme</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    <p><a href="../index.php">← Volver al menú</a></p>
  </main>
  <script src="app.js"></script>
</body>
</html>
