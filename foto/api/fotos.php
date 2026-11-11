<?php
// foto/api/fotos.php
// Servicio que ENTREGA la lista de fotos:  GET api/fotos.php  → JSON

require __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

// 🔗 PISTA LOGIN: si la galería fuera solo para usuarios con sesión,
// aquí se respondería 401 cuando no exista $_SESSION['usuario_id'].

// La lista sale de la tabla fotos (las más nuevas primero).
$fotos = $pdo->query('SELECT nombre, archivo FROM fotos ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(array_map(fn($foto) => [
    'url'     => 'subidas/' . $foto['archivo'],
    'usuario' => $foto['nombre'],
], $fotos));
