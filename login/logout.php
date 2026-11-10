<?php
// login/logout.php
// Borra los datos de la sesión y regresa al login.

session_start();
$_SESSION = [];
session_destroy();

header('Location: login.php');
