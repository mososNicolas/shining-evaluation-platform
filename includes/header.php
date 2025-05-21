<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config.php';
}

$archivoActual = basename($_SERVER['PHP_SELF']);
$esInicio = $archivoActual === 'index.php';
?>

<header class="py-4 <?php echo $esInicio ? 'bg-primary text-white' : 'bg-white border-bottom'; ?>">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo o título que siempre lleva al inicio -->
        <a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none <?php echo $esInicio ? 'text-white' : 'text-dark'; ?>">
            <h1 class="h4 m-0">Shining festival</h1>
        </a>


        <!-- Solo mostramos el usuario logueado si no es el index -->
        <?php if (!$esInicio && isset($_SESSION['nombre'])): ?>
            <span class="text-muted small">Welcome, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
        <?php endif; ?>
    </div>
</header>
