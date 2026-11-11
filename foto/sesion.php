<?php
// foto/sesion.php
// Conecta a la base de datos, inicia la sesión y le da un nombre al usuario.

require __DIR__ . '/conexion.php'; // trae $pdo, session_start() y e()

// ----------------------------------------------------------------------------
// 🔗 PISTA PARA CONECTAR CON LOGIN
// Aquí debería "cacharse" la sesión que crea login.php del ejercicio LOGIN.
// Allá, al iniciar sesión, se guarda:
//     $_SESSION['usuario_id'] = $usuario['id'];
//     $_SESSION['nombre']     = $usuario['nombre'];
// Para conectarlo, este bloque se cambiaría por:
//     if (!isset($_SESSION['usuario_id'])) {
//         header('Location: login.php');   // sin sesión, primero a iniciar sesión
//         exit;
//     }
// Mientras tanto, cada navegador recibe un nombre de invitado al azar
// (y en la tabla fotos su usuario_id queda en NULL).
// ----------------------------------------------------------------------------
if (!isset($_SESSION['nombre'])) {
    $_SESSION['nombre'] = 'invitado' . random_int(100, 999);
}
