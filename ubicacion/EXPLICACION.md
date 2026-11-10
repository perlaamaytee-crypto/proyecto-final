# Mi ubicación: del GPS al mapa (y a la base de datos)

**Ejercicio aislado, pensado para conectarse con LOGIN.** Con un botón, el usuario captura su ubicación (latitud, longitud y precisión). Esa ubicación se guarda en la **misma base `login_app`** de LOGIN y SEGUIR, en una tabla nueva, `ubicaciones`. Las últimas 50 aparecen en un mapa de **OpenStreetMap**.

---

## 1. La idea principal

```
 Navegador                                              Servidor (PHP + MySQL)
 ───────────────────────────────────                    ──────────────────────────────────
  [Capturar mi ubicación]
    navigator.geolocation
      .getCurrentPosition()  → { lat, lng, precisión }
         │
         ├─► mapa (Leaflet + OpenStreetMap)
         │
         └─ POST api/guardar.php ──────────────────►  valida ─► INSERT INTO ubicaciones
            (cuerpo JSON)                               nombre y usuario_id de $_SESSION
                                ◄──── 201 ─────────

  Mapa + historial   ── GET api/ubicaciones.php ──►  SELECT … ORDER BY id DESC LIMIT 50
                     ◄──────── JSON ──────────────
```

Es el mismo patrón que FOTO y SEGUIR: **un servicio que recibe** (`guardar.php`) y **otro que entrega** (`ubicaciones.php`). La página no recarga: JavaScript se comunica con los servicios usando `fetch`.

## 2. Estructura de archivos

```
UBICACION/
├── database.sql       ← agrega la tabla ubicaciones a login_app
├── conexion.php       ← el de LOGIN + nombre de invitado (🔗 pistas LOGIN)
├── index.php          ← la página: botón, mapa e historial
├── ubicacion.js       ← geolocalización, mapa, envío e historial
├── api/
│   ├── guardar.php    ← servicio que RECIBE una ubicación (POST, JSON)
│   └── ubicaciones.php← servicio que ENTREGA el historial (GET, JSON)
└── estilos.css        ← el de LOGIN + estilos del mapa
```

Destino: **`C:\xampp\htdocs\ubicacion\`**, con la subcarpeta `api\` incluida.

## 3. La tabla `ubicaciones`

```sql
CREATE TABLE ubicaciones (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT NULL,               -- FK a usuarios(id); NULL si es invitado
    nombre      VARCHAR(100) NOT NULL,
    latitud     DECIMAL(9, 6) NOT NULL,
    longitud    DECIMAL(9, 6) NOT NULL,
    precision_m INT UNSIGNED NOT NULL,
    creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
```

- **`DECIMAL(9, 6)` y no `FLOAT`**: con 6 decimales la precisión es de unos 10 cm, y `DECIMAL` guarda el número exacto, sin errores de redondeo.
- **`usuario_id` puede ser `NULL`**, igual que en `eventos` de SEGUIR: un invitado también puede guardar su ubicación.
- La columna se llama **`precision_m`** porque `PRECISION` es una palabra reservada de MySQL.

## 4. Capturar la ubicación (`ubicacion.js`)

```js
navigator.geolocation.getCurrentPosition(guardar, fallo, {
    enableHighAccuracy: true, // usar GPS si existe (celular)
    timeout: 10000,           // máximo 10 s
    maximumAge: 0,            // nada de posiciones viejas en caché
});
```

- El navegador pregunta: **"¿Permitir conocer tu ubicación?"**.
- Si el usuario acepta, se llama a `guardar(posicion)` con `posicion.coords.latitude`, `longitude` y `accuracy` (radio de error en metros).
- Si algo falla, se llama a `fallo(error)` con alguno de estos tres códigos:

| `error.code` | Significado |
|---|---|
| 1 | El usuario **negó** el permiso |
| 2 | No se pudo determinar la posición (sin GPS, wifi ni red) |
| 3 | Se agotó el **tiempo** de espera |

### ¿De dónde sale la ubicación?

| Dispositivo | Fuente | Precisión típica |
|---|---|---|
| Celular con GPS | Satélites | 5 a 20 m |
| Laptop con wifi | Redes wifi cercanas | 20 a 100 m |
| PC de escritorio por cable | Dirección IP | ¡Varios kilómetros! |

Por eso se guarda la **precisión** y el mapa dibuja un **círculo** a su alrededor: la ubicación es un área, no un punto exacto.

### ⚠️ Contexto seguro (igual que la cámara en FOTO)

| Dirección | ¿Ubicación? |
|---|---|
| `http://localhost/ubicacion` | ✅ |
| `https://….trycloudflare.com` (túnel) | ✅ también en celulares |
| `http://192.168.1.50/ubicacion` (IP de la red local) | ❌ el navegador la bloquea |

## 5. El mapa: OpenStreetMap + Leaflet

**OpenStreetMap (OSM)** es el "Wikipedia de los mapas": los datos los hacen voluntarios y son libres. **Leaflet** es una librería de JavaScript que toma las **imágenes cuadradas (mosaicos o *tiles*)** de OSM y las arma como mapa, con zoom y arrastre.

```js
const mapa = L.map('mapa').setView([19.43, -99.13], 10);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap',
}).addTo(mapa);
```

- `{z}/{x}/{y}` son el **zoom** y la **posición del mosaico**. Abre F12 → *Network* y mueve el mapa: verás llegar las imágenes `.png`. **Cada mosaico es una petición HTTP.**

| | Google Maps | OpenStreetMap + Leaflet |
|---|---|---|
| API key | Obligatoria, con tarjeta | No necesita |
| Datos | De Google | Libres (licencia ODbL) |
| Condición | Cuotas y cobro por uso | **Dar crédito** (el texto © de la esquina) y no abusar del servidor de mosaicos |

> **Pregunta para clase:** si OSM es gratis, ¿quién paga los servidores de los mosaicos? ¿Qué pasaría si una app con un millón de usuarios los usara directamente?

## 6. Servicio que recibe: `api/guardar.php`

Esta vez **no se usa `FormData`** (como en FOTO): se envía **JSON**.

```js
fetch('api/guardar.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ latitud, longitud, precision }),
});
```

Por eso en PHP **`$_POST` llega vacío** y hay que leer el cuerpo crudo:

```php
$datos = json_decode(file_get_contents('php://input'), true);
```

| Situación | Respuesta HTTP |
|---|---|
| Método distinto a POST | **405** Method Not Allowed |
| No es JSON, faltan datos o están fuera de rango | **400** Bad Request |
| Ubicación guardada | **201** Created + `{ "id": … }` |

**Nunca confiar en el cliente:**
1. Se valida que la latitud esté entre **-90 y 90** y la longitud entre **-180 y 180**.
2. El **nombre** y el **`usuario_id`** salen de `$_SESSION`, no del JSON. Si vinieran del navegador, cualquiera podría guardar ubicaciones a nombre de otra persona.
3. `prepare` + `execute` evitan la inyección SQL.

> **Para probar en clase** (PowerShell):
> ```powershell
> curl.exe -X POST http://localhost/ubicacion/api/guardar.php -H "Content-Type: application/json" -d '{\"latitud\":200,\"longitud\":0}'
> ```
> Responde `400`. Pero con coordenadas válidas **sí se guarda**, aunque sean inventadas. ¿Puede el servidor saber si una ubicación es real?

## 7. Servicio que entrega: `api/ubicaciones.php`

Devuelve las **últimas 50** ubicaciones en JSON y se puede abrir directo en el navegador. Un detalle: MySQL entrega los `DECIMAL` como texto (`"19.432608"`), así que PHP los convierte con `(float)` antes del `json_encode`.

El popup del marcador se arma con un elemento y **`textContent`**, no con un texto HTML, por el mismo motivo que en SEGUIR y FOTO: evitar XSS (un invitado no puede elegir su nombre, pero un usuario de LOGIN sí).

## 8. 🔗 Cómo se conecta con LOGIN

**Ya está medio conectado.** Si alguien inició sesión en `http://localhost/login` y después abre `http://localhost/ubicacion`, la cookie `PHPSESSID` sirve para todo `localhost`: `$_SESSION['usuario_id']` y `$_SESSION['nombre']` ya existen, así que la ubicación se guarda **con su nombre real y su `usuario_id`**.

Lo que falta (comentarios **🔗 PISTA LOGIN**):

| Archivo | Qué cambiaría |
|---|---|
| `conexion.php` | Quitar el invitado y redirigir a `login.php` si no hay `$_SESSION['usuario_id']`. |
| `api/guardar.php` | Responder **401** si no hay sesión. |
| `api/ubicaciones.php` | Mostrar solo mis ubicaciones: `WHERE usuario_id = ?`. |
| Idea extra | En `bienvenida.php` de LOGIN, mostrar "Tu última ubicación" con un mini mapa. |

## 9. 🔒 Privacidad: para discutir en clase

Este ejercicio muestra **a todo el mundo** dónde estuvieron **todos**. Funciona para aprender, pero en una app real sería un problema grave.

- ¿Quién debería ver mi ubicación? ¿Solo yo? ¿Mis contactos?
- ¿Cuánto tiempo debería guardarse? ¿Para siempre?
- Seis decimales permiten distinguir **la casa** de una persona. ¿Conviene redondear a 3 decimales (unos 100 m) antes de guardar?
- En México, la ubicación es un **dato personal** (LFPDPPP): se necesita **aviso de privacidad** y consentimiento. El permiso del navegador **no basta** como aviso.

## 10. Puesta en marcha

1. XAMPP: encender **Apache** y **MySQL**.
2. Si no existe la base `login_app`, ejecutar primero `database.sql` de **LOGIN**.
3. Ejecutar `database.sql` de este ejercicio (phpMyAdmin → pestaña SQL).
4. Copiar todo a `C:\xampp\htdocs\ubicacion\`, **incluida `api`**.
5. Abrir `http://localhost/ubicacion`.

**Para probar con el celular por Cloudflare**, igual que en LOGIN y FOTO:
```text
PowerShell n.º 1:  C:\xampp\php\php.exe -S localhost:8000 -t C:\xampp\htdocs\ubicacion
PowerShell n.º 2:  cloudflared tunnel --url http://localhost:8000
```

## 11. Guía de prueba (para el profesor)

1. Abrir `http://localhost/ubicacion`: arriba aparece `Sesión: invitadoNNN` y un mapa centrado en la CDMX.
2. **Capturar mi ubicación** → aceptar el permiso → el mapa se acerca, dibuja el círculo de precisión y aparece el mensaje verde.
3. F12 → *Network*: `guardar.php` responde **201** y `ubicaciones.php` devuelve el JSON.
4. phpMyAdmin → `login_app` → `ubicaciones`: la fila está ahí, con `usuario_id` en `NULL`.
5. Repetir **negando el permiso** (icono del candado → Ubicación → Bloquear): aparece "Negaste el permiso de ubicación".
6. Iniciar sesión en `http://localhost/login` y volver: ahora aparece el nombre real y `usuario_id` tiene valor.
7. Abrir la app por el túnel desde un celular: comparar su precisión con la de la PC.
8. Mandar coordenadas fuera de rango con `curl` (sección 6) → **400**.
9. Para reiniciar antes de clase: `TRUNCATE TABLE ubicaciones;`

## 12. Retos opcionales

- **Seguir en vivo**: cambiar `getCurrentPosition` por `watchPosition` y mover el marcador mientras se camina.
- **Distancia** entre mi ubicación y la UPVM con la fórmula de Haversine (o con `mapa.distance(a, b)` de Leaflet).
- **Dirección legible**: convertir coordenadas en dirección con el servicio *Nominatim* de OSM (`https://nominatim.openstreetmap.org/reverse?format=json&lat=…&lon=…`). Es otro servicio web, ¡ahora consumido desde fuera!
- **Redondear** a 3 decimales en el servidor antes de guardar (sección 9).
- Hacer la conexión completa con LOGIN de la sección 8.
