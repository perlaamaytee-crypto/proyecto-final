-- Ejecutar en DBeaver, conexión railway (base de datos railway).
-- Solo AGREGA la tabla ubicaciones. La tabla usuarios no se toca.
-- Requisito: que ya exista la tabla usuarios (la del Login).

CREATE TABLE IF NOT EXISTS ubicaciones (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT           NULL,                  -- NULL si es un invitado (sin Login)
    nombre      VARCHAR(100)  NOT NULL,              -- el nombre de la sesión: real o invitadoNNN
    latitud     DECIMAL(9, 6) NOT NULL,              -- -90.000000 a 90.000000
    longitud    DECIMAL(9, 6) NOT NULL,              -- -180.000000 a 180.000000
    precision_m INT UNSIGNED  NOT NULL,              -- radio de error en metros
    creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
