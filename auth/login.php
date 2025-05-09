<?php
session_start();
require_once '../includes/db.php';

$rol = $_GET['rol'] ?? null;
$area = $_GET['area'] ?? null;

// Verificar que los parámetros sean correctos
if (!$rol || ($rol === 'jurado' && !$area)) {
    $error = 'Parámetros inválidos. Intente nuevamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar clave y contraseña
    $clave = $_POST['clave'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    // Preparar consulta según el rol
    if ($rol === 'jurado') {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE clave = ? AND rol = ? AND area = ?");
        $stmt->bind_param("sss", $clave, $rol, $area);
    } else {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE clave = ? AND rol = ?");
        $stmt->bind_param("ss", $clave, $rol);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    // Validar contraseña
    // En login.php, después de validar las credenciales:
    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['area'] = $usuario['area'] ?? null;
        // Resto del código de redirección...

        // Redirección según el rol
        if ($rol === 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($rol === 'jurado' && $area === 'ingles') {
            header("Location: ../jurado/ingles/dashboard.php");
        } elseif ($rol === 'jurado' && $area === 'musica') {
            header("Location: ../jurado/musica/dashboard.php");
        }
        exit;
    } else {
        $error = 'Clave no encontrada o contraseña incorrecta.';
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - <?php echo ucfirst($rol); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-5">
        <h2 class="text-center mb-4">
            Iniciar sesión como 
            <?php echo ($rol === 'jurado') ? ucfirst($area) : ucfirst($rol); ?>
        </h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
                <label for="clave" class="form-label">Clave asignada</label>
                <input type="text" name="clave" id="clave" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>

</body>
</html>
