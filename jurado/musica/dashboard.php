<?php
session_start();
require_once("../../includes/db.php");

if ($_SESSION['rol'] !== 'jurado' || $_SESSION['area'] !== 'musica') {
    header("Location: ../../auth/login.php?rol=jurado&area=musica");
    exit;
}

$id_jurado = $_SESSION['usuario_id'];

// Función para ejecutar consultas con manejo de errores
function ejecutarConsulta($conexion, $sql, $params = [], $types = '') {
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conexion->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando consulta: " . $stmt->error);
    }
    
    return $stmt;
}

try {
    // Procesar evaluación de participante
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evaluar_participante'])) {
        $participante_id = $_POST['participante_id'];
        $afinacion = $_POST['afinacion'];         // Parte de 35%
        $ritmo = $_POST['ritmo'];                 // Parte de 35%
        $proyeccion = $_POST['proyeccion'];       // Parte de 35%
        $interpretacion = $_POST['interpretacion']; // Parte de 35%
        $story_time = $_POST['story_time'];       // Parte de 25%
        $diseno_escenico = $_POST['diseno_escenico']; // Parte de 25%
        $comentarios = $conexion->real_escape_string($_POST['comentarios'] ?? '');

        $sql_evaluar = "INSERT INTO calificaciones_musica 
                       (jurado_id, participante_id, afinacion, ritmo, proyeccion_vocal, 
                        interpretacion, story_time, diseno_escenico, comentarios_musica) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql_evaluar);
        $stmt->bind_param("iiiiiiiis", $id_jurado, $participante_id, $afinacion, $ritmo,
                         $proyeccion, $interpretacion, $story_time, $diseno_escenico, $comentarios);

        if ($stmt->execute()) {
            header("Location: dashboard.php?evaluado=1");
            exit;
        } else {
            die("Error al registrar evaluación: " . $stmt->error);
        }
    }

    // Obtener participantes pendientes de evaluación
    $stmt = ejecutarConsulta(
        $conexion,
        "SELECT p.* FROM participantes p
        LEFT JOIN calificaciones_musica c ON p.id = c.participante_id AND c.jurado_id = ?
        WHERE c.participante_id IS NULL
        ORDER BY p.categoria, p.modalidad",
        [$id_jurado],
        "i"
    );
    
    $pendientes = $stmt->get_result();

    // Obtener participante actual si hay ID en la URL
    $participante_actual = null;
    if (isset($_GET['participante_id'])) {
        $stmt = ejecutarConsulta(
            $conexion,
            "SELECT * FROM participantes WHERE id = ?",
            [$_GET['participante_id']],
            "i"
        );
        $participante_actual = $stmt->get_result()->fetch_assoc();
    }

} catch (Exception $e) {
    die("Error en el sistema: " . $e->getMessage());
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
    </style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include("../../includes/header.php"); ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Sistema de Evaluación - Jurado de Música</h2>
        
        <?php if (isset($_GET['evaluado'])): ?>
            <div class="alert alert-success">Evaluación registrada correctamente</div>
        <?php endif; ?>
        
        <!-- Sección para evaluar participante -->
        <?php if ($participante_actual): ?>
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5>Evaluar Participante: <?= htmlspecialchars($participante_actual['nombre']) ?></h5>
                    <p class="mb-0">
                        <?= ucfirst($participante_actual['categoria']) ?> | 
                        <?= ucfirst($participante_actual['modalidad']) ?> | 
                        <?= htmlspecialchars($participante_actual['colegio']) ?>
                    </p>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="participante_id" value="<?= $participante_actual['id'] ?>">
                        
                        <div class="row">
                            <!-- Aspecto Musical (35%) -->
                            <div class="col-md-6 border-end">
                                <h5 class="text-primary">Aspecto Musical (35%)</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Afinación (0-10 puntos)</label>
                                    <input type="number" name="afinacion" min="0" max="10" class="form-control" required>
                                    <small class="text-muted">Precisión en tonos y armonía</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ritmo (0-10 puntos)</label>
                                    <input type="number" name="ritmo" min="0" max="10" class="form-control" required>
                                    <small class="text-muted">Sincronización y consistencia rítmica</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Proyección Vocal (0-10 puntos)</label>
                                    <input type="number" name="proyeccion" min="0" max="10" class="form-control" required>
                                    <small class="text-muted">Volumen, claridad y control de la voz</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Interpretación (0-5 puntos)</label>
                                    <input type="number" name="interpretacion" min="0" max="5" class="form-control" required>
                                    <small class="text-muted">Expresión emocional y conexión con el público</small>
                                </div>
                                
                                <div class="alert alert-light">
                                    <strong>Total música:</strong> <span id="total_musica">0</span>/35 puntos
                                </div>
                            </div>
                            
                            <!-- Creatividad (25%) -->
                            <div class="col-md-6">
                                <h5 class="text-primary">Creatividad y Visuales (25%)</h5>

                                <div class="mb-3">
                                    <label class="form-label">Story Time (0-10 puntos)</label>
                                    <input type="number" name="story_time" min="0" max="10" class="form-control" required>
                                    <small class="text-muted">Coherencia y originalidad de la historia visual</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Diseño Escénico (0-10 puntos)</label>
                                    <input type="number" name="diseno_escenico" min="0" max="10" class="form-control" required>
                                    <small class="text-muted">Vestuario, escenografía e iluminación</small>
                                </div>

                                <div class="alert alert-light">
                                    <strong>Total Visual:</strong> <span id="total_visual">0</span>/20 puntos
                                    <small class="float-end">(Se escalará al 25% final)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" name="evaluar_participante" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Registrar Evaluación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Sección para participantes pendientes -->
        <?php if ($pendientes->num_rows > 0): ?>
            <div class="card mb-5">
                <div class="card-header bg-warning">
                    <h5>Participantes Pendientes de Evaluación</h5>
                    <p class="mb-0">Total: <?= $pendientes->num_rows ?> participantes</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Modalidad</th>
                                    <th>Colegio</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($p = $pendientes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                                        <td><?= ucfirst($p['categoria']) ?></td>
                                        <td><?= ucfirst($p['modalidad']) ?></td>
                                        <td><?= htmlspecialchars($p['colegio']) ?></td>
                                        <td>
                                            <a href="dashboard.php?participante_id=<?= $p['id'] ?>" class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-right-circle"></i> Evaluar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                No hay participantes pendientes de evaluación musical en este momento.
            </div>
        <?php endif; ?>
    </div>
    
    <script>
    // Cálculo automático de puntajes
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', calcularTotales);
    });
    
    function calcularTotales() {
        // Suma música (4 criterios: 10+10+10+5 = 35 puntos)
        const musica = [
            parseInt(document.querySelector('input[name="afinacion"]').value) || 0,
            parseInt(document.querySelector('input[name="ritmo"]').value) || 0,
            parseInt(document.querySelector('input[name="proyeccion"]').value) || 0,
            parseInt(document.querySelector('input[name="interpretacion"]').value) || 0
        ].reduce((a, b) => a + b, 0);
        
        // Creatividad (1 criterio de 10 puntos)
        const creatividad = parseInt(document.querySelector('input[name="creatividad"]').value) || 0;
        
        document.getElementById('total_musica').textContent = musica;
        document.getElementById('total_creatividad').textContent = creatividad;
    }
    </script>
    
    <?php include("../../includes/footer.php"); ?>
    
</body>
</html>
