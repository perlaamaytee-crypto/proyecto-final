<?php
require __DIR__ . "/../conexion.php";
$resultado = $conexion->query("SELECT * FROM productos");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Productos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .tabla { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; display: inline-block; }
        .btn-add { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; }
        .btn-home { background-color: #6c757d; }
    </style>
</head>
<body>
    <h2>Consulta de Productos (Read)</h2>
    <a href="../index.php" class="btn btn-home">⬅ Menú principal</a>
    <a href="crear.php" class="btn btn-add">Agregar Producto (Create)</a>
    <br><br>
    <div class="tabla">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?php echo (int)$row['id_producto']; ?></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                <td>$<?php echo number_format($row['precio'], 2); ?></td>
                <td><?php echo (int)$row['stock']; ?></td>
                <td>
                    <a href="editar.php?id=<?php echo (int)$row['id_producto']; ?>" class="btn btn-edit">Editar (Update)</a>
                    <a href="eliminar.php?id=<?php echo (int)$row['id_producto']; ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar este producto?');">Eliminar (Delete)</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
