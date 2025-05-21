<?php
// Consulta SQL optimizada
$sql = "SELECT 
    p.id,
    p.nombre,
    p.categoria,
    p.modalidad,
    p.colegio,

    /* Puntaje Inglés - Juez 1 */
    (
        SELECT IFNULL((pronunciacion + fluidez + vocabulario) / 3 * 0.40, 0)
        FROM calificaciones_ingles ci
        JOIN usuarios u ON ci.jurado_id = u.id
        WHERE ci.participante_id = p.id
        ORDER BY u.id ASC
        LIMIT 1
    ) AS puntaje_ingles1,
    
    /* Puntaje Inglés - Juez 2 */
    (
        SELECT IFNULL((pronunciacion + fluidez + vocabulario) / 3 * 0.40, 0)
        FROM calificaciones_ingles ci
        JOIN usuarios u ON ci.jurado_id = u.id
        WHERE ci.participante_id = p.id
        ORDER BY u.id DESC
        LIMIT 1
    ) AS puntaje_ingles2,

    /* Detalle de jueces */
    (
        SELECT GROUP_CONCAT(
            CONCAT(' P=', pronunciacion, ',F=', fluidez, ',V=', vocabulario) SEPARATOR ' | ')
        FROM calificaciones_ingles ci
        JOIN usuarios u ON ci.jurado_id = u.id
        WHERE ci.participante_id = p.id
    ) AS detalle_jueces,

    /* Puntaje Música */
    (
        SELECT IFNULL((afinacion + ritmo + proyeccion_vocal + interpretacion*2) / 4 * 0.35, 0)
        FROM calificaciones_musica
        WHERE participante_id = p.id
        LIMIT 1
    ) AS puntaje_musica,

    /* Puntaje Creatividad (25%) */
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
        ) / 2 * 0.25
    ) AS puntaje_visual,

    /* Total Ponderado */
    (
        /* Inglés (40%) */
        (
            (SELECT IFNULL((pronunciacion + fluidez + vocabulario) / 3 * 0.20, 0)
             FROM calificaciones_ingles ci
             JOIN usuarios u ON ci.jurado_id = u.id
             WHERE ci.participante_id = p.id
             ORDER BY u.id ASC
             LIMIT 1)
            +
            (SELECT IFNULL((pronunciacion + fluidez + vocabulario) / 3 * 0.20, 0)
             FROM calificaciones_ingles ci
             JOIN usuarios u ON ci.jurado_id = u.id
             WHERE ci.participante_id = p.id
             ORDER BY u.id DESC
             LIMIT 1)
        )
        +
        /* Música (35%) */
        (SELECT IFNULL((afinacion + ritmo + proyeccion_vocal + interpretacion*2) / 4 * 0.35, 0)
         FROM calificaciones_musica
         WHERE participante_id = p.id
         LIMIT 1)
        +
        /* Creatividad (25%) */
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
            ) / 2 * 0.25
        )
    ) AS total

FROM participantes p
LEFT JOIN calificaciones_ingles ci ON p.id = ci.participante_id
LEFT JOIN calificaciones_musica cm ON p.id = cm.participante_id
GROUP BY p.id, p.nombre, p.categoria, p.modalidad, p.colegio
ORDER BY p.categoria, p.modalidad, total DESC";

$resultado = $conexion->query($sql);
if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}

// Preparar datos para DataTables
$data = array();

while ($fila = $resultado->fetch_assoc()) {
    $data[] = array(
        'id' => $fila['id'],
        'nombre' => htmlspecialchars($fila['nombre']),
        'categoria' => htmlspecialchars($fila['categoria']),
        'modalidad' => htmlspecialchars($fila['modalidad']),
        'colegio' => htmlspecialchars($fila['colegio']),
        'ingles1' => number_format($fila['puntaje_ingles1'], 2),
        'ingles2' => number_format($fila['puntaje_ingles2'], 2),
        'musica' => number_format($fila['puntaje_musica'], 2),
        'visual' => number_format($fila['puntaje_visual'], 2),
        'total' => ($fila['total'] > 0) ? number_format($fila['total'], 2) : 0,
        'detalle' => $fila['detalle_jueces'] ?? 'N/A'
    );
}

// Procesar nuevo participante si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_participante'])) {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $colegio = $conexion->real_escape_string($_POST['colegio']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);
    $modalidad = $conexion->real_escape_string($_POST['modalidad']);
    
    $sql_insert = "INSERT INTO participantes (nombre, colegio, categoria, modalidad) 
                  VALUES ('$nombre', '$colegio', '$categoria', '$modalidad')";
    
    if ($conexion->query($sql_insert)) {
        header("Location: ../dashboard.php?nuevo=1");
        exit;
    } else {
        die("Error al registrar participante: " . $conexion->error);
    }
}
?>