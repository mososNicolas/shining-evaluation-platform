<?php
session_start();
require_once("../includes/db.php");

if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php?rol=admin");
    exit;
}

// Consulta SQL corregida con ponderaciones y estructura adecuada
$sql = "SELECT 
    p.id,
    p.nombre,
    p.categoria,
    p.modalidad,

    /* Inglés (40%) - Promedio individual de 3 criterios entre 2 jurados */
    (
        (
            COALESCE(AVG(ci.pronunciacion), 0) +
            COALESCE(AVG(ci.fluidez), 0) +
            COALESCE(AVG(ci.vocabulario), 0)
        ) / 3
    ) * 0.40 AS puntaje_ingles,

    /* Música (35%) - Único jurado, promedio de 4 criterios */
    (
        (
            COALESCE(MAX(cm.afinacion), 0) +
            COALESCE(MAX(cm.ritmo*2), 0) +
            COALESCE(MAX(cm.proyeccion_vocal), 0) +
            COALESCE(MAX(cm.interpretacion), 0)
        ) / 4
    ) * 0.35 AS puntaje_musica,

    /* Creatividad (25%) - Promedio de visuales entre inglés (promedio de 2 jurados) y música (único jurado) */
    (
        (
            (
                COALESCE(AVG(ci.story_time), 0) + 
                COALESCE(AVG(ci.diseno_escenico), 0)
            ) / 2
            +
            (
                COALESCE(MAX(cm.story_time), 0) + 
                COALESCE(MAX(cm.diseno_escenico), 0)
            ) / 2
        ) / 2
    ) * 0.25 AS puntaje_visual,

    /* Total real ponderado */
    (
        (
            (
                COALESCE(AVG(ci.pronunciacion), 0) +
                COALESCE(AVG(ci.fluidez), 0) +
                COALESCE(AVG(ci.vocabulario), 0)
            ) / 3 * 0.40
        ) +
        (
            (
                COALESCE(MAX(cm.afinacion), 0) +
                COALESCE(MAX(cm.ritmo), 0) +
                COALESCE(MAX(cm.proyeccion_vocal), 0) +
                COALESCE(MAX(cm.interpretacion), 0)
            ) / 4 * 0.35
        ) +
        (
            (
                (
                    (COALESCE(AVG(ci.story_time), 0) + COALESCE(AVG(ci.diseno_escenico), 0)) / 2
                    +
                    (COALESCE(MAX(cm.story_time), 0) + COALESCE(MAX(cm.diseno_escenico), 0)) / 2
                ) / 2
            ) * 0.25
        )
    ) AS total

FROM participantes p
LEFT JOIN calificaciones_ingles ci ON p.id = ci.participante_id
LEFT JOIN calificaciones_musica cm ON p.id = cm.participante_id
GROUP BY p.id, p.nombre, p.categoria, p.modalidad
ORDER BY p.categoria, p.modalidad, total DESC";

$resultado = $conexion->query($sql);
if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}

// Registro de nuevo participante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_participante'])) {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);
    $modalidad = $conexion->real_escape_string($_POST['modalidad']);
    $colegio = $conexion->real_escape_string($_POST['colegio']);

    $sql_insert = "INSERT INTO participantes (nombre, categoria, modalidad, colegio) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("ssss", $nombre, $categoria, $modalidad, $colegio);

    if ($stmt->execute()) {
        header("Location: dashboard.php?participante_id=" . $conexion->insert_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
        }
        footer {
            background-color: #222;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .group-header {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }
        .print-only {
            display: none;
        }
        @media print {
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
            body {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<?php include("../includes/header.php"); ?>

<div class="container mt-4">
    <h2 class="mb-4">Resultados Generales</h2>

    <!-- Tabla de resultados -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered w-100" id="tabla-resultados">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Modalidad</th>
                    <th>Inglés (40%)</th>
                    <th>Música (35%)</th>
                    <th>Creatividad (25%)</th>
                    <th>Puntaje Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $categoria_actual = '';
                $modalidad_actual = '';
                while ($fila = $resultado->fetch_assoc()):
                    if ($fila['categoria'] !== $categoria_actual || $fila['modalidad'] !== $modalidad_actual):
                        $categoria_actual = $fila['categoria'];
                        $modalidad_actual = $fila['modalidad'];
                ?>
                    <tr class="group-header">
                        <td colspan="7"><?= strtoupper($categoria_actual . " - " . $modalidad_actual) ?></td>
                    </tr>
                <?php endif; ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['nombre']) ?></td>
                        <td><?= htmlspecialchars($fila['categoria']) ?></td>
                        <td><?= htmlspecialchars($fila['modalidad']) ?></td>
                        <td><?= number_format($fila['puntaje_ingles'], 2) ?></td>
                        <td><?= number_format($fila['puntaje_musica'], 2) ?></td>
                        <td><?= number_format($fila['puntaje_visual'], 2) ?></td>
                        <td><strong><?= number_format($fila['total'], 2) ?></strong></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <button onclick="window.print()" class="btn btn-success mb-5 no-print">Imprimir Resultados</button>

    <!-- Formulario de nuevo participante (oculto al imprimir) -->
    <div class="card no-print">
        <div class="card-header bg-info text-white">
            <h5>Registrar Nuevo Participante</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Colegio</label>
                            <input type="text" name="colegio" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select" required>
                                <option value="kids">Kids (1°-5°)</option>
                                <option value="teens">Teens (6°-9°)</option>
                                <option value="seniors">Seniors (10°-11°)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Modalidad</label>
                            <select name="modalidad" class="form-select" required>
                                <option value="solistas">Solista</option>
                                <option value="grupos">Grupo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" name="nuevo_participante" class="btn btn-primary">
                    Registrar Participante
                </button>
            </form>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>