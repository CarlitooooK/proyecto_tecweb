<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'src/Recurso.php';

requireAdmin();

use App\Recurso;

$db = Database::getInstance()->getConnection();
$recurso = new Recurso($db);
$recursos = $recurso->obtenerTodos();

registrarAcceso('Acceso a dashboard');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>🎛️ Dashboard Administrativo</h2>
            </div>
            <div class="nav-menu">
                <span>Admin: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="index.php" class="btn btn-secondary">Ver Catálogo</a>
                <a href="logout.php" class="btn btn-outline">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Panel de Control</h1>
        </div>

        <div class="stats-grid">
            <?php
            $stmt = $db->query("SELECT COUNT(*) as total FROM recursos WHERE eliminado = 0");
            $totalRecursos = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1");
            $totalUsuarios = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM bitacora_descargas");
            $totalDescargas = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM bitacora_acceso WHERE DATE(fecha_hora) = CURDATE()");
            $accesosHoy = $stmt->fetch()['total'];
            ?>
            
            <div class="stat-card">
                <h3>Total Recursos</h3>
                <p class="stat-number"><?php echo $totalRecursos; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Usuarios Activos</h3>
                <p class="stat-number"><?php echo $totalUsuarios; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Descargas</h3>
                <p class="stat-number"><?php echo $totalDescargas; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Accesos Hoy</h3>
                <p class="stat-number"><?php echo $accesosHoy; ?></p>
            </div>
        </div>

        <div class="charts-section">
            <h2>Estadísticas de Descargas</h2>
            
            <div class="charts-grid">
                <div class="chart-card">
                    <h3>Descargas por Tipo de Recurso</h3>
                    <canvas id="chartTipoRecurso"></canvas>
                </div>
                
                <div class="chart-card">
                    <h3>Descargas por Día de la Semana</h3>
                    <canvas id="chartDiaSemana"></canvas>
                </div>
                
                <div class="chart-card">
                    <h3>Descargas por Hora del Día</h3>
                    <canvas id="chartHoraDia"></canvas>
                </div>
                
                <div class="chart-card">
                    <h3>Descargas por Lenguaje de Programación</h3>
                    <canvas id="chartLenguaje"></canvas>
                </div>
            </div>
        </div>

        <div class="table-section">
            <h2>Gestión de Recursos</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Autor</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recursos as $r): ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['nombre_recurso']); ?></td>
                        <td><?php echo htmlspecialchars($r['autor']); ?></td>
                        <td><span class="badge"><?php echo $r['tipo_recurso']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($r['fecha_creacion'])); ?></td>
                        <td>
                            <button class="btn-small btn-primary" onclick="editarRecurso(<?php echo $r['id']; ?>)">Editar</button>
                            <button class="btn-small btn-danger" onclick="eliminarRecurso(<?php echo $r['id']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>
