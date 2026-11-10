<?php
// ubicacion/conexion.php
// Conecta a MySQL con PDO y arranca la sesión de PHP.
// En Render lee las variables DB_HOST, DB_PORT, DB_USER, DB_PASS y DB_NAME.
// Si no existen (por ejemplo en XAMPP), usa localhost con la base login_app.

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$name = getenv('DB_NAME') ?: 'login_app';

$pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

session_start();

// Si la persona inició sesión en el Login, la sesión ya trae usuario_id y nombre.
// Si no, cada navegador recibe un nombre de invitado al azar.
if (!isset($_SESSION['nombre'])) {
    $_SESSION['nombre'] = 'invitado' . random_int(100, 999);
}

// Escapa texto antes de mostrarlo en la página (evita que se cuele código HTML).
function e($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
