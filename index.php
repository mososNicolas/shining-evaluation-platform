<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Shining Like a Star</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
        <!-- Añade este estilo para asegurar el sticky footer -->
        <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
        </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Contenido principal (DEBE envolver TODO excepto el footer) -->
    <main class="flex-grow-1">
        <!-- HERO / BANNER -->
        <header class="text-white bg-dark py-5 text-center">
            <div class="container">
                <h1 class="display-4 fw-bold">Shining Like a Star ✨</h1>
                <p class="lead">Celebrando el talento, el idioma y el arte en nuestra comunidad educativa</p>
            </div>
        </header>

        <!-- DESCRIPCIÓN GENERAL -->
        <section class="container py-5">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body">
                            <h3 class="card-title">🎤 Participantes</h3>
                            <p class="card-text">Estudiantes de todos los grados compiten en categorías según edad y nivel.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body">
                            <h3 class="card-title">🧑‍⚖️ Jurados</h3>
                            <p class="card-text">Expertos en música e inglés evalúan con criterios objetivos y artísticos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body">
                            <h3 class="card-title">📅 Día del Evento</h3>
                            <p class="card-text">Presentaciones ágiles y emocionantes, organizadas por horarios específicos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ACCESO A PANELES -->
        <section class="bg-white py-5 border-top">
            <div class="container text-center">
                <h2 class="mb-4">Accede a tu panel</h2>
                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <a href="auth/login.php?rol=admin" class="btn btn-primary w-100">👨‍💼 Rector</a>
                    </div>
                    <div class="col-md-3">
                        <a href="auth/login.php?rol=jurado&area=ingles" class="btn btn-secondary w-100">🌍 Jurado de Inglés</a>
                    </div>
                    <div class="col-md-3">
                        <a href="auth/login.php?rol=jurado&area=musica" class="btn btn-warning w-100">🎵 Jurado de Música</a>
                    </div>
                </div>
            </div>
        </section>
    </main> <!-- ¡CIERRA EL MAIN ANTES DEL FOOTER! -->

    <!-- FOOTER (Incluido desde PHP) -->
    <?php include("includes/footer.php"); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>