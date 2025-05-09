<?php
// Configuración de conexión
$host = "localhost";
$usuario = "root";       // cámbialo si usas otro usuario
$contrasena = "";        // cámbialo si tu usuario tiene contraseña
$base_datos = "shining_festival";

// Crear conexión
$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
