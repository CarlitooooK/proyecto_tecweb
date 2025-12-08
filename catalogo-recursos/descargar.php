<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'src/Recurso.php';

use App\Recurso;

$id = $_GET['id'] ?? 0;

$db = Database::getInstance()->getConnection();
$recurso = new Recurso($db);
$recursoData = $recurso->obtenerPorId($id);

if (!$recursoData) {
    die('Recurso no encontrado');
}

$recurso->registrarDescarga($id);
registrarAcceso('Descarga recurso: ' . $recursoData['nombre_recurso']);

$filePath = __DIR__ . '/' . $recursoData['archivo_ruta'];

if (file_exists($filePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($recursoData['archivo_original']) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    die('Archivo no encontrado en el servidor');
}
?>
