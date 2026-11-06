# Ejercicio_Chat — Versión 1

Chat sencillo con Flask. El servidor expone una API REST y guarda los mensajes en una lista en memoria (sin base de datos). Los navegadores consultan mensajes nuevos cada 2 segundos.

## Estructura

```
Ejercicio_Chat/
├── app.py               Servidor Flask (API REST)
├── requirements.txt     Dependencias
├── README.md
└── templates/
    └── index.html       Cliente (HTML + CSS + JavaScript)
```

## 1. Ejecutar en local (Windows)

Desde la terminal de VS Code, dentro de la carpeta `Ejercicio_Chat`:

```
python -m venv venv
venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

Abrir http://localhost:5000 en dos pestañas con nombres distintos para probar.

Si PowerShell bloquea `activate`, ejecutar una vez:
`Set-ExecutionPolicy -Scope CurrentUser RemoteSigned`

## 2. Compartir en la web desde VS Code

1. Con `python app.py` corriendo, abrir el panel **Ports** (Puertos), junto a la Terminal.
2. **Forward a Port** → escribir `5000`. VS Code pide iniciar sesión con GitHub.
3. Clic derecho en el puerto → **Port Visibility** → **Public**.
4. Copiar la dirección que aparece en **Forwarded Address** y compartirla.

Mientras la visibilidad sea *Private*, los demás tendrán que iniciar sesión con GitHub. El enlace deja de funcionar al cerrar VS Code o detener el servidor.

## 3. Desplegar en Render (opcional)

1. Subir la carpeta a un repositorio de GitHub.
2. En Render: **New → Web Service** → conectar el repositorio.
3. Build Command: `pip install -r requirements.txt`
4. Start Command: `gunicorn app:app --workers 1 --threads 4`

Se usa **un solo worker** a propósito: cada worker es un proceso con su propia memoria. Con dos o más, cada uno tendría una lista de mensajes distinta y los usuarios no se verían entre sí.

## Probar la API sin navegador

```
curl http://localhost:5000/api/estado
curl -X POST http://localhost:5000/api/mensajes -H "Content-Type: application/json" -d "{\"usuario\":\"Ana\",\"texto\":\"Hola\"}"
curl "http://localhost:5000/api/mensajes?desde=0"
```

## Limitaciones (intencionales en esta versión)

- Los mensajes se pierden al reiniciar el servidor.
- Solo se conservan los últimos 200 mensajes.
- No hay autenticación: cualquiera puede escribir con cualquier nombre.
- El cliente pregunta cada 2 s aunque no haya mensajes nuevos (polling).
