<?php
// Generar hashes seguros con password_hash()
$credenciales = [
    'rector123',
    'ingles123',
    'ingles456',
    'musica123'
];

foreach ($credenciales as $clave) {
    echo "Contraseña: $clave\nHash: " . password_hash($clave, PASSWORD_DEFAULT) . "\n\n";
}
?>
