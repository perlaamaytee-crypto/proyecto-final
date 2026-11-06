<?php
require __DIR__ . "/../conexion.php";

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: index.php");
exit();
?>
