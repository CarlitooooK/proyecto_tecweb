<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'src/Recurso.php';

use App\Recurso;

$db = Database::getInstance()->getConnection();
$recurso = new Recurso($db);
$recursos = $recurso->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Recursos</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>📚 Catálogo de Recursos</h2>
            </div>
            <div class="nav-menu">
                <?php if (isLoggedIn()): ?>
                    <span>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    <?php if (isAdmin()): ?>
                        <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                    <?php endif; ?>
                    <a href="cargar-recurso.php" class="btn btn-primary">Subir Recurso</a>
                    <a href="logout.php" class="btn btn-outline">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
                    <a href="signup.php" class="btn btn-outline">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Recursos Disponibles</h1>
            <p>Explora y descarga recursos compartidos por la comunidad</p>
        </div>

        <div class="filters">
            <input type="text" id="searchInput" placeholder="Buscar recursos..." class="search-input">
            <select id="filterTipo" class="filter-select">
                <option value="">Todos los tipos</option>
                <option value="documento">Documentos</option>
                <option value="codigo">Código</option>
                <option value="imagen">Imágenes</option>
                <option value="video">Videos</option>
                <option value="otro">Otros</option>
            </select>
        </div>

        <div class="recursos-grid" id="recursosGrid">
            <?php foreach ($recursos as $r): ?>
                <div class="recurso-card" data-tipo="<?php echo $r['tipo_recurso']; ?>">
                    <div class="recurso-icon">
                        <?php
                        $icons = [
                            'documento' => '📄',
                            'codigo' => '💻',
                            'imagen' => '🖼️',
                            'video' => '🎥',
                            'otro' => '📦'
                        ];
                        echo $icons[$r['tipo_recurso']] ?? '📦';
                        ?>
                    </div>
                    <h3><?php echo htmlspecialchars($r['nombre_recurso']); ?></h3>
                    <p class="recurso-autor">Por: <?php echo htmlspecialchars($r['autor']); ?></p>
                    <p class="recurso-descripcion"><?php echo htmlspecialchars($r['descripcion']); ?></p>
                    
                    <div class="recurso-meta">
                        <span class="badge"><?php echo htmlspecialchars($r['tipo_recurso']); ?></span>
                        <?php if ($r['lenguaje_programacion']): ?>
                            <span class="badge badge-language"><?php echo htmlspecialchars($r['lenguaje_programacion']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="recurso-info">
                        <small>📅 <?php echo date('d/m/Y', strtotime($r['fecha_creacion'])); ?></small>
                        <small>🏢 <?php echo htmlspecialchars($r['empresa_institucion']); ?></small>
                    </div>
                    
                    <button class="btn btn-primary btn-block" onclick="descargarRecurso(<?php echo $r['id']; ?>)">
                        Descargar
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        function descargarRecurso(id) {
            window.location.href = 'descargar.php?id=' + id;
        }
    </script>
</body>
</html>
