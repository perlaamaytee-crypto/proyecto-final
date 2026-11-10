<?php
// login/login.php
// Formulario de inicio de sesión y la lógica que revisa correo y contraseña.

require 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // Buscamos al usuario por correo. El ? evita inyección SQL.
    $stmt = $pdo->prepare('SELECT id, nombre, password_hash FROM usuarios WHERE correo = ?');
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // password_verify compara lo escrito con el hash guardado.
    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre']     = $usuario['nombre'];
        header('Location: bienvenida.php');
        exit;
    }
    // Mismo mensaje si falla el correo o la contraseña, para no dar pistas.
    $error = 'Correo o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="../estilos.css">
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <header>
    <img src="../foto.png" alt="Mi foto">
    <h1>Iniciar sesión</h1>
    <p>Perla Nava</p>
  </header>
  <main>
    <?php if (isset($_GET['registrado'])): ?>
      <p class="exito">Cuenta creada. Ya puedes iniciar sesión.</p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post">
      <label>Correo
        <input type="email" name="correo" required value="<?= e($_POST['correo'] ?? '') ?>">
      </label>
      <label>Contraseña
        <input type="password" name="password" required>
      </label>
      <button class="btn" type="submit">Entrar</button>
    </form>

    <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    <p><a href="../index.php">← Volver al menú</a></p>
  </main>
</body>
</html>
