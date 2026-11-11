-- Ejecutar en DBeaver, conexión railway (base de datos railway).
-- Solo AGREGA la tabla fotos. La tabla usuarios no se toca.
-- Requisito: que ya exista la tabla usuarios (la del Login).

CREATE TABLE IF NOT EXISTS fotos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT          NULL,             -- NULL = invitado (sin Login)
    nombre     VARCHAR(100) NOT NULL,         -- nombre de la sesión: invitado482 o el del Login
    archivo    VARCHAR(150) NOT NULL UNIQUE,  -- nombre del archivo en subidas/
    tipo       VARCHAR(20)  NOT NULL,         -- image/jpeg, image/png...
    bytes      INT UNSIGNED NOT NULL,         -- tamaño del archivo
    creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
