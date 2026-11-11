# Subir foto: de la cámara (o el disco) al servidor

**Ejercicio aislado, pensado para conectarse después con LOGIN.** El usuario toma una foto con la cámara de la PC o del celular, o elige una de su equipo. La foto se guarda en el servidor con su nombre de sesión y aparece en una galería.

Hay **dos lugares de guardado**: la imagen se guarda como **archivo** en la carpeta `subidas/` y su **registro** (quién, qué archivo, cuándo) va a la tabla `fotos` de la base `login_app`, la misma de LOGIN y SEGUIR.

---

## 1. La idea principal

```
 Navegador                                          Servidor (PHP + MySQL)
 ─────────────────────────────────                  ──────────────────────────────────
  Opción 1: cámara en vivo
    getUserMedia → <video> → <canvas> → JPG ─┐
                                             ├─ POST api/subir.php ──►  valida ─► archivo en subidas/
  Opción 2: <input type="file"> ─────────────┘   (FormData)                     ─► INSERT INTO fotos
                                                ◄──── JSON (201) ────    nombre = usuario de $_SESSION
  Galería                    ── GET api/fotos.php ──►  SELECT … FROM fotos
                             ◄──────── JSON ─────────
```

Igual que en SEGUIR, hay **un servicio que recibe** (`subir.php`) y **otro que entrega** (`fotos.php`). La página no recarga: JavaScript habla con los servicios usando `fetch`.

## 2. Estructura de archivos

```
FOTO/
├── database.sql       ← crea la tabla fotos en login_app
├── conexion.php       ← igual que en LOGIN: PDO + session_start() + e()
├── index.php          ← la página: cámara, archivo, vista previa y galería
├── sesion.php         ← usa conexion.php y da un nombre de invitado (🔗 pistas LOGIN)
├── foto.js            ← cámara, vista previa, subida y galería
├── api/
│   ├── subir.php      ← servicio que RECIBE la foto (POST)
│   └── fotos.php      ← servicio que ENTREGA la lista de fotos (GET, JSON)
├── subidas/
│   └── .htaccess      ← impide ejecutar código en esta carpeta
└── estilos.css        ← el de LOGIN + estilos de cámara y galería
```

Destino: **`C:\xampp\htdocs\foto\`**, incluidas las carpetas `api\` y `subidas\`.

## 3. Base de datos: la tabla `fotos`

```sql
CREATE TABLE fotos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT          NULL,             -- NULL = invitado (todavía sin LOGIN)
    nombre     VARCHAR(100) NOT NULL,         -- nombre de la sesión
    archivo    VARCHAR(150) NOT NULL UNIQUE,  -- nombre del archivo en subidas/
    tipo       VARCHAR(20)  NOT NULL,         -- image/jpeg, image/png…
    bytes      INT UNSIGNED NOT NULL,
    creado_en  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
```

- **La imagen NO se guarda en la base**, solo su nombre de archivo. Así trabajan casi todos los sitios: los archivos van en disco (o en un servicio como S3) y la base guarda la referencia. Las tablas se mantienen ligeras y las imágenes se sirven directo.
- **`usuario_id NULL` + `nombre`**: los invitados no existen en `usuarios`, por eso se guarda su nombre de sesión. Cuando se conecte con LOGIN, `usuario_id` tendrá el `id` real.
- **`FOREIGN KEY … ON DELETE SET NULL`**: es el mismo diseño que la tabla `eventos` de SEGUIR.
- `database.sql` también trae `CREATE TABLE IF NOT EXISTS usuarios`, igual que en LOGIN. Si la tabla ya existe no se toca; si no, se crea para que este ejercicio funcione por sí solo.

> **Para discutir:** si se borra un archivo de `subidas/` a mano, la tabla todavía lo menciona. ¿Cómo se detectaría? ¿Y al revés?

## 4. La sesión: ¿quién sube la foto?

`sesion.php` incluye `conexion.php` (que hace `session_start()`) y, si el navegador todavía no tiene nombre, le asigna uno al azar:

```php
if (!isset($_SESSION['nombre'])) {
    $_SESSION['nombre'] = 'invitado' . random_int(100, 999);
}
```

El nombre vive **en el servidor** (`$_SESSION`). El navegador solo guarda la cookie `PHPSESSID`, que funciona como la llave de esa sesión. Por eso `subir.php` **no recibe el nombre desde el formulario**: lo lee de la sesión. Si el nombre viniera del navegador, cualquiera podría subir fotos a nombre de otra persona.

## 5. La cámara en vivo (`foto.js`)

```js
video.srcObject = await navigator.mediaDevices.getUserMedia({ video: true }); // pide permiso
...
lienzo.getContext('2d').drawImage(video, 0, 0);   // "congela" el cuadro actual
lienzo.toBlob(usarFoto, 'image/jpeg', 0.9);        // lo convierte en un archivo JPG
```

- **`getUserMedia`**: el navegador pregunta "¿Permitir usar la cámara?". Si el usuario dice que no, se ve el error y queda la opción de subir un archivo.
- **Tomar foto = dibujar el video en un `<canvas>`**: el canvas no aparece en la página, solo sirve para obtener la imagen.
- **`playsinline`** en el `<video>`: sin este atributo, el iPhone abre el video en pantalla completa.

### ⚠️ Contexto seguro

El navegador **solo da acceso a la cámara** en:

| Dirección | ¿Cámara? |
|---|---|
| `http://localhost/foto` | ✅ |
| `https://….trycloudflare.com` (túnel) | ✅ también en celulares |
| `http://192.168.1.50/foto` (IP de la red local) | ❌ `navigator.mediaDevices` no existe |

> **Pregunta para clase:** ¿por qué el navegador pide HTTPS para usar la cámara y no para mostrar una página?

## 6. Las dos opciones llegan al mismo lugar

La foto de la cámara (`Blob`) y la del archivo (`File`) se tratan igual: `usarFoto()` las guarda en una variable y muestra la **vista previa** con `URL.createObjectURL`, **antes** de que la foto llegue al servidor. Después se envían con:

```js
const datos = new FormData();
datos.append('foto', foto, 'foto.jpg');
fetch('api/subir.php', { method: 'POST', body: datos });
```

`FormData` arma una petición `multipart/form-data`, igual que un `<form enctype="multipart/form-data">`. Del lado de PHP, la foto llega en `$_FILES['foto']`.

## 7. Servicio que recibe: `api/subir.php`

| Situación | Respuesta HTTP |
|---|---|
| Método distinto a POST | **405** Method Not Allowed |
| No llegó archivo, pesa más de 5 MB o no es imagen | **400** Bad Request |
| No se pudo escribir en `subidas/` o en la tabla `fotos` | **500** Internal Server Error |
| Foto guardada | **201** Created + `{ "archivo": …, "url": … }` |

**Cuatro reglas de seguridad al subir archivos:**

1. **Revisar el contenido, no la extensión.** `finfo` lee los primeros bytes del archivo. Un `hack.php` renombrado como `hack.jpg` se rechaza.
2. **El servidor pone el nombre.** Se usa `invitado482_20261021-073506_4e64.jpg` y nunca el nombre original: así no se pisan las fotos de otros ni se cuelan nombres como `../../index.php`.
3. **Límite de tamaño** (5 MB en el código; PHP también tiene `upload_max_filesize` en `php.ini`).
4. **`subidas/.htaccess`** prohíbe ejecutar `.php` en esa carpeta. Así hay una segunda barrera por si fallara la primera.

> **Para probar en clase:** crear `falso.jpg` con el contenido `<?php echo "hack";` y subirlo. El servicio responde `400`.

## 8. Servicio que entrega: `api/fotos.php`

Hace `SELECT nombre, archivo FROM fotos ORDER BY id DESC` (las más nuevas primero) y arma la URL `subidas/archivo`. Se puede abrir directo en el navegador para ver el JSON crudo.

**Orden en `subir.php`:** primero se mueve el archivo a `subidas/` y después se hace el `INSERT`. Si el `INSERT` falla, se borra el archivo (`unlink`) para que no quede una foto sin registro.

La galería pone el nombre con **`textContent`**, no con `innerHTML`, por el mismo motivo que en SEGUIR: evitar XSS.

## 9. 🔗 Cómo se conectaría con LOGIN (pistas, no está hecho)

En el código aparecen comentarios con **🔗 PISTA LOGIN**:

| Archivo | Qué cambiaría |
|---|---|
| `sesion.php` | En vez del invitado al azar: si no existe `$_SESSION['usuario_id']`, redirigir a `login.php`. El nombre ya viene en `$_SESSION['nombre']`, porque lo guarda `login.php`. `conexion.php` es el mismo en las dos apps. |
| `api/subir.php` | Responder **401** si no hay `$_SESSION['usuario_id']`. El `INSERT` no cambia: ya guarda `$_SESSION['usuario_id'] ?? null`, así que la foto quedaría ligada al usuario real. |
| `api/fotos.php` | Igual, si la galería fuera solo para usuarios con sesión. |
| Idea extra | En `bienvenida.php`: `SELECT archivo FROM fotos WHERE usuario_id = ? ORDER BY id DESC LIMIT 1` para mostrarla como foto de perfil. |

> **Dato curioso para clase:** si alguien ya inició sesión en `http://localhost/login` y después abre `http://localhost/foto`, ¡esta app ya sabe su nombre real! La cookie `PHPSESSID` vale para todo `localhost`, así que `$_SESSION['nombre']` ya existe y no se crea un invitado. ¿Es una ventaja o un riesgo?

## 10. Puesta en marcha

1. XAMPP: encender **Apache** y **MySQL**.
2. Ejecutar `database.sql` en phpMyAdmin (pestaña SQL). Crea la tabla `fotos` en `login_app`.
3. Copiar todo a `C:\xampp\htdocs\foto\`, **incluidas `api` y `subidas`**.
4. Abrir `http://localhost/foto`.

**Para compartir con Cloudflare y probar con el celular**, igual que en LOGIN:
```text
PowerShell n.º 1:  C:\xampp\php\php.exe -S localhost:8000 -t C:\xampp\htdocs\foto
PowerShell n.º 2:  cloudflared tunnel --url http://localhost:8000
```
Nota: el servidor `php -S` **no lee `.htaccess`** (eso es de Apache). La regla 1 (`finfo`) sigue protegiendo, pero conviene comentarlo en clase.

## 11. Guía de prueba (para el profesor)

1. Abrir `http://localhost/foto`: arriba aparece `Sesión: invitadoNNN`.
2. **Activar cámara** → aceptar el permiso → **Tomar foto** → aparece la vista previa → **Subir foto** → mensaje verde y la foto en la galería.
3. Elegir un archivo del equipo y subirlo: el flujo es el mismo.
4. F12 → *Network*: `subir.php` responde **201** y `fotos.php` devuelve el JSON.
5. Abrir una ventana de incógnito: sale **otro** invitado y la galería muestra las fotos de los dos.
6. Subir `falso.jpg` (sección 7) → **400**.
7. En phpMyAdmin: `SELECT * FROM login_app.fotos;` → una fila por foto válida, con `usuario_id` en NULL (invitados).
8. Abrir la app por el túnel desde un celular y tomar una foto con su cámara.
9. Para reiniciar antes de clase: `TRUNCATE login_app.fotos;` y borrar las fotos de `subidas\` **sin borrar `.htaccess`**.

## 12. Retos opcionales

- Botón **"Cambiar cámara"** en el celular: `getUserMedia({ video: { facingMode: 'environment' } })`.
- **Reducir la foto** en el canvas (por ejemplo a 800 px de ancho) antes de subirla.
- **Borrar** mis fotos: `DELETE api/fotos.php?archivo=…`, permitido solo si la foto es del usuario de la sesión.
- Hacer la conexión con LOGIN de la sección 9.
