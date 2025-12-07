<?php
include_once __DIR__.'/database.php'; 
include_once __DIR__.'/resource-validation.php'; 
include_once __DIR__.'/filetype-icon.php';

$inputJSON = file_get_contents('php://input');
$recurso = json_decode($inputJSON, true);

// Validación inicial de ID
$id = $recurso['id'] ?? null;
if (empty($id)) {
    echo json_encode(["status" => "validation_error", "message" => "Error de edición: ID de recurso no recibido."]);
    exit;
}

$errors = validateProductData($recurso);
if (!empty($errors)) {
    echo json_encode([
        "status" => "validation_error",
        "message" => "Errores de validación encontrados.",
        "details" => $errors
    ]);
    exit;
}

// Obtener y escapar datos
$nombre_recurso = $conexion->real_escape_string($recurso['nombre_recurso'] ?? '');
$autor = $conexion->real_escape_string($recurso['autor'] ?? '');
$departamento = $conexion->real_escape_string($recurso['departamento'] ?? '');
$empresa = $conexion->real_escape_string($recurso['empresa'] ?? '');
$fecha_creacion = $conexion->real_escape_string($recurso['fecha_creacion'] ?? '');
$descripcion = $conexion->real_escape_string($recurso['descripcion'] ?? '');
$archivo = $conexion->real_escape_string($recurso['archivo'] ?? '');
$id_sql = $conexion->real_escape_string($id);

// Logo automático
$logo = assignLogoByExtension($archivo);

$query = "UPDATE resourcesbd SET
            nombre_recurso = '$nombre_recurso',
            autor = '$autor',
            departamento = '$departamento',
            empresa = '$empresa',
            fecha_creacion = '$fecha_creacion',
            descripcion = '$descripcion',
            archivo = '$archivo',
            logo = '$logo'
          WHERE id = '$id_sql' AND eliminado = 0";

$result = $conexion->query($query);

if ($result) {
    if ($conexion->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Recurso actualizado correctamente."]);
    } else {
        echo json_encode(["status" => "info", "message" => "No se realizaron cambios o el recurso no existe."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error al actualizar en BD: " . $conexion->error]);
}

$conexion->close();
?>