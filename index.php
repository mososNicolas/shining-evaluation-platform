<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shining Like a Star</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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

<main class="flex-grow-1">
    <header class="text-center py-5" style="background-color: var(--main-blue);">
        <img src="uploads/image.png" alt="Shining Like a Star Logo" class="img-fluid mb-3" style="max-height: 150px;">
        <h1 class="display-5 fw-bold text-white">Are you ready to shine?</h1>
        <p class="fs-5 text-warning">"Shining Like a Star"</p>
    </header>

    <section class="container py-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h3 class="card-title">🎤 Participants</h3>
                        <p class="card-text">Students from all grades compete in categories based on age and level.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h3 class="card-title">🧑‍⚖️ Judges</h3>
                        <p class="card-text">Music and English experts evaluate using clear and artistic criteria.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h3 class="card-title">📅 Event Day</h3>
                        <p class="card-text">Fast-paced and exciting performances, carefully scheduled by time slots.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <h2 class="text-center mb-4" style="color: var(--main-blue);">Event Schedule</h2>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Stage</th>
                        <th>Description</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Opening</td>
                        <td>Welcome, introduction of judges, acknowledgments</td>
                        <td>15 minutes</td>
                    </tr>
                    <tr>
                        <td>Kids (Solo & Group)</td>
                        <td>20 performances (2 min each + 1 min video)</td>
                        <td>~30 minutes</td>
                    </tr>
                    <tr>
                        <td>Teens (Solo & Group)</td>
                        <td>16 performances (same format)</td>
                        <td>~30 minutes</td>
                    </tr>
                    <tr>
                        <td>Seniors (Solo & Group)</td>
                        <td>16 performances (same format)</td>
                        <td>~30 minutes</td>
                    </tr>
                    <tr>
                        <td>Break</td>
                        <td>Interaction with guests and sponsors</td>
                        <td>15–20 minutes</td>
                    </tr>
                    <tr>
                        <td>Special Performance</td>
                        <td>Guest school presents a non-competing act in English</td>
                        <td>10 minutes</td>
                    </tr>
                    <tr>
                        <td>Awards Ceremony</td>
                        <td>Announcement of winners and prize distribution</td>
                        <td>30 minutes</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-muted text-center mt-3">Total duration: Approximately 3.5 to 4 hours</p>
    </section>

    <section class="bg-white py-5 border-top">
        <div class="container text-center">
            <h2 class="mb-4" style="color: var(--main-blue);">Access your panel</h2>
            <div class="row justify-content-center">
                <div class="col-md-3 m-2">
                    <a href="auth/login.php?rol=admin" class="btn btn-primary w-100">👨‍💼 Bilingualism Coordination</a>
                </div>
                <div class="col-md-3 m-2">
                    <a href="auth/login.php?rol=jurado&area=ingles" class="btn btn-secondary w-100">🌍 English Judges</a>
                </div>
                <div class="col-md-3 m-2">
                    <a href="auth/login.php?rol=jurado&area=musica" class="btn btn-warning w-100">🎵 Music Judge</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include("includes/footer.php"); ?>

    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
