// foto/foto.js

const video       = document.getElementById('video');
const btnCamara   = document.getElementById('btn-camara');
const btnTomar    = document.getElementById('btn-tomar');
const archivo     = document.getElementById('archivo');
const vistaPrevia = document.getElementById('vista-previa');
const btnSubir    = document.getElementById('btn-subir');
const mensaje     = document.getElementById('mensaje');
const galeria     = document.getElementById('galeria');

let foto = null; // la imagen lista para subir (venga de la cámara o del archivo)

function mostrar(texto, clase) {
    mensaje.textContent = texto;
    mensaje.className = clase;
    mensaje.hidden = false;
}

// Las dos opciones terminan aquí: guardan la foto y muestran la vista previa.
// URL.createObjectURL crea una dirección temporal para ver la imagen
// ANTES de mandarla al servidor.
function usarFoto(imagen) {
    foto = imagen;
    vistaPrevia.src = URL.createObjectURL(imagen);
    vistaPrevia.hidden = false;
    btnSubir.disabled = false;
}

// ---------- Opción 1: cámara en vivo ----------
btnCamara.addEventListener('click', async () => {
    // El navegador solo da acceso a la cámara en https:// o en localhost.
    if (!navigator.mediaDevices) {
        mostrar('La cámara solo funciona en https:// o en localhost. Usa la opción de archivo.', 'error');
        return;
    }
    try {
        // Aquí el navegador pregunta: "¿Permitir usar la cámara?"
        video.srcObject = await navigator.mediaDevices.getUserMedia({ video: true });
        video.hidden = false;
        btnTomar.disabled = false;
    } catch (error) {
        mostrar('No se pudo abrir la cámara: ' + error.message, 'error');
    }
});

btnTomar.addEventListener('click', () => {
    // "Tomar foto" = dibujar el cuadro actual del video en un canvas y convertirlo a JPG.
    const lienzo = document.createElement('canvas');
    lienzo.width = video.videoWidth;
    lienzo.height = video.videoHeight;
    lienzo.getContext('2d').drawImage(video, 0, 0);
    lienzo.toBlob(usarFoto, 'image/jpeg', 0.9);
});

// ---------- Opción 2: archivo del equipo ----------
archivo.addEventListener('change', () => {
    if (archivo.files[0]) usarFoto(archivo.files[0]);
});

// ---------- Subir: el mismo servicio para las dos opciones ----------
btnSubir.addEventListener('click', async () => {
    const datos = new FormData();
    datos.append('foto', foto, 'foto.jpg'); // el servidor ignora este nombre y pone el suyo

    btnSubir.disabled = true;
    const respuesta = await fetch('api/subir.php', { method: 'POST', body: datos });
    const json = await respuesta.json();
    btnSubir.disabled = false;

    if (!respuesta.ok) {
        mostrar(json.error, 'error');
        return;
    }
    mostrar('Foto guardada como ' + json.archivo, 'exito');
    cargarGaleria();
});

// ---------- Galería: pide la lista al servicio y la dibuja ----------
async function cargarGaleria() {
    const fotos = await (await fetch('api/fotos.php')).json();
    galeria.innerHTML = '';
    for (const f of fotos) {
        const figura = document.createElement('figure');
        const img = document.createElement('img');
        const pie = document.createElement('figcaption');
        img.src = f.url;
        img.alt = 'Foto de ' + f.usuario;
        pie.textContent = f.usuario; // textContent, nunca innerHTML (evita XSS)
        figura.append(img, pie);
        galeria.append(figura);
    }
}

cargarGaleria();
