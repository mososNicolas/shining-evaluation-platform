<?php
session_start();
require_once("../includes/db.php");
require_once("controlador/controlador.php");

if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php?rol=admin");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .container {
            flex: 1;
            padding-top: 20px;
        }
        footer {
            background-color: #222;
            color: white;
            padding: 1rem;
            text-align: center;
            margin-top: 20px;
        }
        .group-header {
            background-color: #e9ecef;
            font-weight: bold;
            text-transform: uppercase;
        }
        .participant-row td {
            vertical-align: middle;
        }
        .detail-info {
            font-size: 0.85em;
            color: #6c757d;
            display: block;
            margin-top: 3px;
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
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .total-cell {
            font-weight: bold;
            color: #2c3e50;
        }
        .no-calification {
            color: #dc3545;
            font-weight: bold;
        }

        .dropdown-menu {
            position: fixed !important;  /* Cambiado de absolute a fixed */
            transform: none !important;
            top: auto !important;
            left: auto !important;
            margin: 0 !important;
            will-change: transform;
            z-index: 1060 !important;
        }

        .dataTables_scrollBody {
            overflow: visible !important;
        }

        .table.dataTable {
            margin-bottom: 0 !important;
        }

        /* Asegura que el contenedor padre no limite el dropdown */
        .dataTables_wrapper .dataTables_scroll {
            overflow: visible !important;
        }
    </style>
</head>
<body>

<?php include("../includes/header.php"); ?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">Resultados Generales</h2>

    <div class="table-responsive mb-4">
        <table class="table table-striped table-hover w-100" id="tabla-resultados">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Modalidad</th>
                    <th>Inglés #1 (20%)</th>
                    <th>Inglés #2 (20%)</th>
                    <th>Música (35%)</th>
                    <th>Creatividad (25%)</th>
                    <th>Puntaje Total</th>
                    <th class="no-print">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán via AJAX -->
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between mb-5 no-print">
        <button onclick="window.print()" class="btn btn-success">
            <i class="bi bi-printer"></i> Imprimir Resultados
        </button>
        <button id="exportExcel" class="btn btn-primary">
            <i class="bi bi-file-excel"></i> Exportar a Excel
        </button>
    </div>

<?php
// Agrupar por categoría y modalidad
$agrupados = [];

foreach ($data as $p) {
    $clave = $p['categoria'] . '|' . $p['modalidad'];
    $total = floatval($p['total']);

    if (!is_numeric($total) || $total <= 0) continue;

    $agrupados[$clave][] = $p;
}

// Ordenar y sacar top 3 de cada grupo
$top3 = [];

foreach ($agrupados as $clave => $participantes) {
    usort($participantes, fn($a, $b) => floatval($b['total']) <=> floatval($a['total']));
    $top3[$clave] = array_slice($participantes, 0, 3);
}
?>

<div class="card mt-5">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-star-fill"></i> Mejores 3 por Categoría y Modalidad</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($top3)): ?>
            <?php foreach ($top3 as $clave => $participantes): ?>
                <?php
                    [$categoria, $modalidad] = explode('|', $clave);
                    $titulo = "Top 3 - " . ucfirst($categoria) . " - " . ucfirst($modalidad);
                ?>
                <h6 class="mt-4 text-primary fw-bold"><?= $titulo ?></h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-success">
                            <tr>
                                <th>Posición</th>
                                <th>Nombre</th>
                                <th>Colegio</th>
                                <th>Puntaje Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participantes as $index => $p): ?>
                                <tr>
                                    <td>
                                        <?php if ($index === 0): ?>
                                            🥇
                                        <?php elseif ($index === 1): ?>
                                            🥈
                                        <?php elseif ($index === 2): ?>
                                            🥉
                                        <?php endif; ?>
                                        <?= $index + 1 ?>
                                    </td>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><?= htmlspecialchars($p['colegio']) ?></td>
                                    <td class="fw-bold"><?= number_format($p['total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-circle"></i> No hay participantes con calificaciones aún.
            </div>
        <?php endif; ?>
    </div>
</div>

    <div class="card no-print">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-person-plus"></i> Registrar Nuevo Participante</h5>
        </div>
        <div class="card-body">
            <form method="POST" id="formParticipante">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" name="nombre" class="form-control" id="nombreInput" required>
                            <label for="nombreInput">Nombre Completo</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" name="colegio" class="form-control" id="colegioInput" required>
                            <label for="colegioInput">Colegio</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select name="categoria" class="form-select" id="categoriaSelect" required>
                                <option value="kids">Kids (1°-5°)</option>
                                <option value="teens">Teens (6°-9°)</option>
                                <option value="seniors">Seniors (10°-11°)</option>
                            </select>
                            <label for="categoriaSelect">Categoría</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select name="modalidad" class="form-select" id="modalidadSelect" required>
                                <option value="solistas">Solista</option>
                                <option value="grupos">Grupo</option>
                            </select>
                            <label for="modalidadSelect">Modalidad</label>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" name="nuevo_participante" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Registrar Participante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include("../includes/footer.php"); ?>

<!-- jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap (solo bundle, incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Botones de DataTables + JSZip -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function () {
    // Convertir datos PHP a JavaScript
    var tableData = <?php echo json_encode($data); ?>;

    // Inicializar DataTable directamente con los datos
    var table = $('#tabla-resultados').DataTable({
        data: tableData,
        columns: [
            { data: 'nombre' },
            { data: 'categoria' },
            { data: 'modalidad' },
            { 
                data: 'ingles1',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data + '<span class="detail-info">' + (row.detalle.split(' | ')[0] || 'N/A') + '</span>';
                    }
                    return data;
                }
            },
            { 
                data: 'ingles2',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data + '<span class="detail-info">' + (row.detalle.split(' | ')[1] || 'N/A') + '</span>';
                    }
                    return data;
                }
            },
            { data: 'musica' },
            { data: 'visual' },
            { 
                data: 'total',
                className: 'total-cell',
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (parseFloat(data) > 0) {
                            return '<strong>' + data + '</strong>';
                        } else {
                            return '<span class="no-calification">Sin calificación</span>';
                        }
                    }
                    return data;
                }
            },
            { 
                data: 'id',
                className: 'no-print',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return `
                            <div class="d-flex">
                                <div class="dropdown me-1">
                                    <button class="btn btn-sm btn-danger dropdown-toggle" type="button" id="dropdownIngles${data}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-trash"></i> Inglés
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownIngles${data}">
                                        <li><a class="dropdown-item delete-btn" href="#" data-id="${data}" data-type="ingles" data-jurado="2">Jurado Inglés 1</a></li>
                                        <li><a class="dropdown-item delete-btn" href="#" data-id="${data}" data-type="ingles" data-jurado="3">Jurado Inglés 2</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item delete-btn" href="#" data-id="${data}" data-type="ingles">Todos los jueces</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-warning dropdown-toggle" type="button" id="dropdownMusica${data}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-trash"></i> Música
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMusica${data}">
                                        <li><a class="dropdown-item delete-btn" href="#" data-id="${data}" data-type="musica" data-jurado="4">Jurado Música</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item delete-btn" href="#" data-id="${data}" data-type="musica">Todos los jueces</a></li>
                                    </ul>
                                </div>
                            </div>
                        `;
                    }
                    return data;
                }
            }
        ],
        order: [[1, 'asc'], [2, 'asc'], [7, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-excel"></i> Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        drawCallback: function(settings) {
            // Reinicializar los dropdowns de Bootstrap después de cada redibujado
            $('.dropdown-toggle').dropdown();
        },
        responsive: true
    });

    $('#exportExcel').on('click', function() {
        table.button('.buttons-excel').trigger();
    });

    // Manejar clics en los botones de dropdown manualmente
    $(document).on('click', '.dropdown-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Cerrar otros dropdowns abiertos
        $('.dropdown-menu').not($(this).next('.dropdown-menu')).hide();
        
        // Alternar el dropdown actual
        $(this).next('.dropdown-menu').toggle();
    });

    // Cerrar dropdowns al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').hide();
        }
    });

    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const participanteId = $(this).data('id');
        const tipo = $(this).data('type');
        const juradoId = $(this).data('jurado');

        const mensaje = juradoId 
            ? `¿Está seguro que desea eliminar las calificaciones de ${tipo} del jurado seleccionado para este participante?`
            : `¿Está seguro que desea eliminar TODAS las calificaciones de ${tipo} para este participante?`;

        if (confirm(mensaje)) {
            $.ajax({
                url: 'controlador/eliminar_calificaciones.php',
                method: 'POST',
                data: {
                    participante_id: participanteId,
                    tipo: tipo,
                    jurado_id: juradoId || ''
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Calificaciones eliminadas correctamente');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.error || 'No se pudo eliminar'));
                    }
                },
                error: function() {
                    alert('Error en la conexión con el servidor');
                }
            });
        }
    });
});
</script>

</body>
</html>