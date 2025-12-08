<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/Recurso.php';

use App\Recurso;

$method = $_SERVER['REQUEST_METHOD'];

$endpoint = $_GET['endpoint'] ?? '';

$db = Database::getInstance()->getConnection();
$recurso = new Recurso($db);

try {
    switch ($endpoint) {
        case 'recursos':
            if ($method === 'GET') {
                $recursos = $recurso->obtenerTodos();
                echo json_encode(['success' => true, 'data' => $recursos]);
            }
            break;
            
        case 'estadisticas':
            if ($method === 'GET') {
                $tipo = $_GET['tipo'] ?? 'tipo_recurso';
                $estadisticas = obtenerEstadisticas($db, $tipo);
                echo json_encode(['success' => true, 'data' => $estadisticas]);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Endpoint no encontrado']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function obtenerEstadisticas($db, $tipo) {
    switch ($tipo) {
        case 'tipo_recurso':
            $stmt = $db->query("
                SELECT r.tipo_recurso, COUNT(d.id) as total_descargas
                FROM recursos r
                LEFT JOIN bitacora_descargas d ON r.id = d.recurso_id
                WHERE r.eliminado = 0
                GROUP BY r.tipo_recurso
            ");
            return $stmt->fetchAll();
            
        case 'lenguaje':
            $stmt = $db->query("
                SELECT r.lenguaje_programacion, COUNT(d.id) as total_descargas
                FROM recursos r
                LEFT JOIN bitacora_descargas d ON r.id = d.recurso_id
                WHERE r.eliminado = 0 AND r.lenguaje_programacion IS NOT NULL
                GROUP BY r.lenguaje_programacion
            ");
            return $stmt->fetchAll();
            
        case 'dia_semana':
            $stmt = $db->query("
                SELECT dia_semana, COUNT(*) as total
                FROM bitacora_descargas
                GROUP BY dia_semana
                ORDER BY FIELD(dia_semana, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
            ");
            return $stmt->fetchAll();
            
        case 'hora_dia':
            $stmt = $db->query("
                SELECT hora_dia, COUNT(*) as total
                FROM bitacora_descargas
                GROUP BY hora_dia
                ORDER BY hora_dia
            ");
            return $stmt->fetchAll();
    }
}
?>
