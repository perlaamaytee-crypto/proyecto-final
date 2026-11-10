// login/app.js
// Antes de enviar el registro, revisa en el navegador que las dos contraseñas sean iguales.

const formRegistro = document.getElementById('form-registro');

formRegistro.addEventListener('submit', (evento) => {
    const password = formRegistro.password.value;
    const confirmar = formRegistro.confirmar.value;

    if (password !== confirmar) {
        evento.preventDefault();
        alert('Las contraseñas no coinciden.');
    }
});
