// ubicacion/ubicacion.js

const btnCapturar = document.getElementById('btn-capturar');
const mensaje     = document.getElementById('mensaje');
const datos       = document.getElementById('datos');
const historial   = document.getElementById('historial');

function mostrar(texto, clase) {
    mensaje.textContent = texto;
    mensaje.className = clase;
    mensaje.hidden = false;
}

// ---------- El mapa: Leaflet + mosaicos de OpenStreetMap ----------
// OpenStreetMap no pide API key, pero SÍ pide dar crédito (attribution).
const mapa = L.map('mapa').setView([19.43, -99.13], 10); // Ciudad de México
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(mapa);

const capaGuardadas = L.featureGroup().addTo(mapa); // marcadores que vienen del servidor
const capaActual    = L.layerGroup().addTo(mapa);   // círculo de precisión de mi captura

// ---------- Capturar: el navegador pregunta "¿Permitir conocer tu ubicación?" ----------
btnCapturar.addEventListener('click', () => {
    // Igual que la cámara en FOTO: solo funciona en https:// o en localhost.
    if (!window.isSecureContext || !navigator.geolocation) {
        mostrar('La ubicación solo funciona en https:// o en localhost.', 'error');
        return;
    }
    btnCapturar.disabled = true;
    mostrar('Buscando tu ubicación…', 'info');
    navigator.geolocation.getCurrentPosition(guardar, fallo, {
        enableHighAccuracy: true, // usar GPS si existe (celular)
        timeout: 10000,           // máximo 10 s esperando
        maximumAge: 0,            // no aceptar una posición vieja guardada en caché
    });
});

const errores = {
    1: 'Negaste el permiso de ubicación.',      // PERMISSION_DENIED
    2: 'No se pudo determinar tu ubicación.',   // POSITION_UNAVAILABLE
    3: 'Se agotó el tiempo de espera.',         // TIMEOUT
};

function fallo(error) {
    btnCapturar.disabled = false;
    mostrar(errores[error.code] ?? error.message, 'error');
}

async function guardar(posicion) {
    const { latitude, longitude, accuracy } = posicion.coords;

    // Primero se muestra en el mapa, sin esperar al servidor.
    capaActual.clearLayers();
    L.circle([latitude, longitude], { radius: accuracy }).addTo(capaActual);
    mapa.setView([latitude, longitude], 16);
    mostrarDatos(latitude, longitude, accuracy);

    // Después se envía al servicio como JSON.
    const respuesta = await fetch('api/guardar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ latitud: latitude, longitud: longitude, precision: accuracy }),
    });
    const json = await respuesta.json();
    btnCapturar.disabled = false;

    if (!respuesta.ok) {
        mostrar(json.error, 'error');
        return;
    }
    mostrar('Ubicación guardada en la base de datos.', 'exito');
    cargarHistorial();
}

function mostrarDatos(lat, lng, precision) {
    const enlace = document.createElement('a');
    enlace.href = `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}#map=17/${lat}/${lng}`;
    enlace.target = '_blank';
    enlace.textContent = 'Abrir en OpenStreetMap';
    datos.textContent = `Lat ${lat.toFixed(6)}, Lng ${lng.toFixed(6)} (±${Math.round(precision)} m) · `;
    datos.append(enlace);
    datos.hidden = false;
}

// ---------- Historial: pide la lista al servicio y la dibuja ----------
async function cargarHistorial() {
    const ubicaciones = await (await fetch('api/ubicaciones.php')).json();
    capaGuardadas.clearLayers();
    historial.innerHTML = '';
    for (const u of ubicaciones) {
        const texto = `${u.nombre} · ${u.creado_en}`;

        // El popup recibe un ELEMENTO con textContent, no texto HTML (evita XSS).
        const etiqueta = document.createElement('span');
        etiqueta.textContent = texto;
        L.marker([u.latitud, u.longitud]).bindPopup(etiqueta).addTo(capaGuardadas);

        const li = document.createElement('li');
        li.textContent = `${texto} → ${u.latitud.toFixed(5)}, ${u.longitud.toFixed(5)} (±${u.precision_m} m)`;
        historial.append(li);
    }
}

// Al abrir la página: cargar el historial y encuadrar el mapa para ver todos los puntos.
cargarHistorial().then(() => {
    if (capaGuardadas.getLayers().length) {
        mapa.fitBounds(capaGuardadas.getBounds(), { maxZoom: 16, padding: [20, 20] });
    }
});
