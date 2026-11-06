-- Encuesta
CREATE TABLE IF NOT EXISTS respuestas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  carrera VARCHAR(100) NOT NULL,
  profesores VARCHAR(120) NOT NULL,
  instalaciones VARCHAR(120) NOT NULL,
  biblioteca VARCHAR(120) NOT NULL,
  cafeteria VARCHAR(120) NOT NULL,
  recomendacion VARCHAR(120) NOT NULL,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CRUD
CREATE TABLE IF NOT EXISTS productos (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO respuestas (nombre, carrera, profesores, instalaciones, biblioteca, cafeteria, recomendacion)
VALUES ('Prueba', 'Ingeniería Industrial', 'Buenos, dan puntos extra (a veces)', 'Bien, solo les falta Netflix', 'Excelente para siestas culturales', 'Regular, la fila es eterna', '¡Claro, hasta les hago publicidad!');

INSERT INTO productos (nombre, descripcion, precio, stock)
VALUES ('Producto de prueba', 'Descripción de ejemplo', 99.50, 10);
