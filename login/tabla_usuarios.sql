-- Ejecutar en DBeaver, conexión railway (base de datos railway).
-- Solo crea la tabla. No hace falta crear otra base de datos.

CREATE TABLE IF NOT EXISTS usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(100) NOT NULL,
    correo        VARCHAR(150) NOT NULL UNIQUE,
    sexo          ENUM('Hombre', 'Mujer', 'Otro') NOT NULL,
    edad          TINYINT UNSIGNED NOT NULL,
    carrera       VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    creado_en     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
