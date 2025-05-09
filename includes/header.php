<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define la URL base del proyecto
if (!defined('BASE_URL')) {
    define('BASE_URL', '/ShiningLikeAStar/');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Shining Like a Star</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>

<!-- NAVBAR CON TÍTULO CENTRADO -->
<nav class="navbar navbar-dark" style="background-color: #1e1e3f;">
    <div class="container d-flex justify-content-center">
        <a class="navbar-brand text-center text-light fw-bold fs-4" href="<?= BASE_URL ?>index.php">
            🌟 <span style="color: #ffd700;">Shining</span> Like a Star
        </a>
    </div>
</nav>
