<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/Recurso.php';

//requireAdmin();

use App\Recurso;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? 0;

$db = Database::getInstance()->getConnection();
$recurso = new Recurso($db);

$resultado = $recurso->eliminar($id);

echo json_encode(['success' => $resultado]);
?>
