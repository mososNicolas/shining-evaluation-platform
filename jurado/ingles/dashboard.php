<?php
session_start();
require_once("../../includes/db.php");

if ($_SESSION['rol'] !== 'jurado' || $_SESSION['area'] !== 'ingles') {
    header("Location: ../../auth/login.php?rol=jurado&area=ingles");
    exit;
}

$id_jurado = $_SESSION['usuario']['id'];

// 1. Procesar registro de nuevo participante
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

// 2. Procesar evaluación de participante (solo inglés y creatividad)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evaluar_participante'])) {
    $participante_id = $_POST['participante_id'];
    $pronunciacion = $_POST['pronunciacion']; // 40%
    $fluidez = $_POST['fluidez'];             // Parte de 40%
    $vocabulario = $_POST['vocabulario'];     // Parte de 40%
    $creatividad = $_POST['creatividad'];     // 25%
    $comentarios = $conexion->real_escape_string($_POST['comentarios'] ?? '');
    
    $sql_evaluar = "INSERT INTO calificaciones_ingles 
                   (jurado_id, participante_id, pronunciacion, fluidez, vocabulario, creatividad, comentarios) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql_evaluar);
    $stmt->bind_param("iiiiiis", $id_jurado, $participante_id, $pronunciacion, 
                     $fluidez, $vocabulario, $creatividad, $comentarios);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?evaluado=1");
        exit;
    }
}

// 3. Obtener participante para evaluar
$participante_actual = null;
if (isset($_GET['participante_id'])) {
    $sql_participante = "SELECT * FROM participantes WHERE id = ?";
    $stmt = $conexion->prepare($sql_participante);
    $stmt->bind_param("i", $_GET['participante_id']);
    $stmt->execute();
    $participante_actual = $stmt->get_result()->fetch_assoc();
}

// 4. Obtener participantes pendientes
// Verificación de conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta SQL con verificación de tabla
$sql_pendientes = "SELECT p.* FROM participantes p
                  LEFT JOIN calificaciones_ingles c ON p.id = c.participante_id AND c.jurado_id = ?
                  WHERE c.participante_id IS NULL
                  ORDER BY p.categoria, p.modalidad";

$stmt = $conexion->prepare($sql_pendientes);
if ($stmt === false) {
    die("Error preparando consulta: " . $conexion->error . " - Consulta: " . $sql_pendientes);
}

$bind_result = $stmt->bind_param("i", $id_jurado);
if ($bind_result === false) {
    die("Error vinculando parámetros: " . $stmt->error);
}

$execute_result = $stmt->execute();
if ($execute_result === false) {
    die("Error ejecutando consulta: " . $stmt->error);
}

$pendientes = $stmt->get_result();
if ($pendientes === false) {
    die("Error obteniendo resultados: " . $stmt->error);
}
?>

<?php include("../../includes/header.php"); ?>

<div class="container mt-4">
    <h2 class="mb-4">Evaluación - Jurado de Inglés</h2>
    
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
                        <!-- Uso del Inglés (40%) -->
                        <div class="col-md-6 border-end">
                            <h5 class="text-primary">Uso del Inglés (40%)</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Pronunciación (0-10 puntos)</label>
                                <input type="number" name="pronunciacion" min="0" max="10" class="form-control" required>
                                <small class="text-muted">Claridad y precisión en la pronunciación</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Fluidez (0-10 puntos)</label>
                                <input type="number" name="fluidez" min="0" max="10" class="form-control" required>
                                <small class="text-muted">Naturalidad y continuidad al hablar</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Vocabulario (0-10 puntos)</label>
                                <input type="number" name="vocabulario" min="0" max="10" class="form-control" required>
                                <small class="text-muted">Adecuación y variedad de palabras</small>
                            </div>
                            
                            <div class="alert alert-light">
                                <strong>Total inglés:</strong> <span id="total_ingles">0</span>/30 puntos
                                <small class="float-end">(Se escalará al 40% final)</small>
                            </div>
                        </div>
                        
                        <!-- Creatividad (25%) -->
                        <div class="col-md-6">
                            <h5 class="text-primary">Creatividad (25%)</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Creatividad y Presentación (0-10 puntos)</label>
                                <input type="number" name="creatividad" min="0" max="10" class="form-control" required>
                                <small class="text-muted">Originalidad en la presentación escénica</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Comentarios Adicionales</label>
                                <textarea name="comentarios" class="form-control" rows="3" placeholder="Observaciones sobre el desempeño..."></textarea>
                            </div>
                            
                            <div class="alert alert-light">
                                <strong>Total creatividad:</strong> <span id="total_creatividad">0</span>/10 puntos
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
    
    <!-- Sección para registrar nuevo participante -->
    <div class="card mb-4">
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
                    <i class="bi bi-person-plus"></i> Registrar Participante
                </button>
            </form>
        </div>
    </div>
    
    <!-- Sección para participantes pendientes -->
    <?php if ($pendientes->num_rows > 0): ?>
        <div class="card">
            <div class="card-header bg-warning">
                <h5>Participantes Pendientes de Evaluación</h5>
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
            No hay participantes pendientes de evaluación. Puede registrar un nuevo participante.
        </div>
    <?php endif; ?>
</div>

<script>
// Cálculo automático de puntajes
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', calcularTotales);
});

function calcularTotales() {
    // Suma inglés (3 criterios de 10 puntos cada uno)
    const ingles = [
        parseInt(document.querySelector('input[name="pronunciacion"]').value) || 0,
        parseInt(document.querySelector('input[name="fluidez"]').value) || 0,
        parseInt(document.querySelector('input[name="vocabulario"]').value) || 0
    ].reduce((a, b) => a + b, 0);
    
    // Creatividad (1 criterio de 10 puntos)
    const creatividad = parseInt(document.querySelector('input[name="creatividad"]').value) || 0;
    
    document.getElementById('total_ingles').textContent = ingles;
    document.getElementById('total_creatividad').textContent = creatividad;
}
</script>

<?php include("../../includes/footer.php"); ?>