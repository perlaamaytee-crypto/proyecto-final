<?php
require __DIR__ . "/../conexion.php";

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = (int) $_POST['stock'];

    $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=? WHERE id_producto=?");
    $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
</head>
<body>
    <h2>Editar Producto #<?php echo $id; ?> (Update)</h2>
    <form method="POST" action="">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required><br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" required><?php echo htmlspecialchars($row['descripcion']); ?></textarea><br><br>

        <label>Precio:</label><br>
        <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($row['precio']); ?>" required><br><br>

        <label>Stock:</label><br>
        <input type="number" name="stock" value="<?php echo (int)$row['stock']; ?>" required><br><br>

        <button type="submit">Actualizar Producto</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
