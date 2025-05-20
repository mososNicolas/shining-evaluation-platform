<?php

// Consulta SQL 
$sql = "SELECT 
    p.id,
    p.nombre,
    p.categoria,
    p.modalidad,

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
        SELECT IFNULL((afinacion + ritmo + proyeccion_vocal + interpretacion) / 4 * 0.35, 0)
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
        (SELECT IFNULL((afinacion + ritmo + proyeccion_vocal + interpretacion) / 4 * 0.35, 0)
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
GROUP BY p.id, p.nombre, p.categoria, p.modalidad
ORDER BY p.categoria, p.modalidad, total DESC";

$resultado = $conexion->query($sql);
if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}

// Preparar datos para DataTables - VERSIÓN MODIFICADA
$data = array();

while ($fila = $resultado->fetch_assoc()) {
    // Solo agregamos filas de participantes, omitimos las de grupo
    $data[] = array(
        'nombre' => htmlspecialchars($fila['nombre']),
        'categoria' => htmlspecialchars($fila['categoria']),
        'modalidad' => htmlspecialchars($fila['modalidad']),
        'ingles1' => number_format($fila['puntaje_ingles1'], 2),
        'ingles2' => number_format($fila['puntaje_ingles2'], 2),
        'musica' => number_format($fila['puntaje_musica'], 2),
        'visual' => number_format($fila['puntaje_visual'], 2),
        'total' => ($fila['total'] > 0) ? number_format($fila['total'], 2) : 0,
        'detalle' => $fila['detalle_jueces'] ?? 'N/A'
    );
    
    // HEMOS ELIMINADO COMPLETAMENTE EL BLOQUE QUE GENERABA LAS FILAS DE GRUPO
}
?>