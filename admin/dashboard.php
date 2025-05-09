<?php
session_start();
require_once("../includes/db.php");

if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php?rol=admin");
    exit;
}

// Consulta adaptada a la nueva estructura
$sql = "
SELECT p.id, p.nombre, p.categoria, p.modalidad,
       (
           COALESCE(SUM(ci.pronunciacion + ci.fluidez + ci.vocabulario + ci.creatividad_ingles), 0) +
           COALESCE(SUM(cm.afinacion + cm.ritmo + cm.proyeccion_vocal + cm.interpretacion + cm.creatividad_musica), 0)
       ) AS total
FROM participantes p
LEFT JOIN calificaciones_ingles ci ON p.id = ci.participante_id
LEFT JOIN calificaciones_musica cm ON p.id = cm.participante_id
GROUP BY p.id, p.nombre, p.categoria, p.modalidad
ORDER BY total DESC
";

$resultado = $conexion->query($sql);
if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>

        html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }
    
    body > .container {
        flex: 1;
    }
    
    footer {
        margin-top: auto;
    }
    </style>

</head>
<body>
    
    <?php include("../includes/header.php"); ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Resultados Generales</h2>
        <div class="table-responsive">
            <table class="table table-bordered" id="tabla-resultados">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Modalidad</th>
                        <th>Puntaje Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['nombre']) ?></td>
                            <td><?= htmlspecialchars($fila['categoria']) ?></td>
                            <td><?= htmlspecialchars($fila['modalidad']) ?></td>
                            <td><?= htmlspecialchars($fila['total']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <button onclick="window.print()" class="btn btn-success mt-3">Imprimir</button>
    </div>
    
    <?php include("../includes/footer.php"); ?>
</body>
</html>
