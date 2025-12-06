<?php
include('database.php'); 
include('resource-validation.php'); 

$inputJSON = file_get_contents('php://input');
$recurso = json_decode($inputJSON, true);

// Validación inicial de ID para edición
$id = $recurso['id'] ?? null;
if (empty($id)) {
    echo json_encode(["status" => "validation_error", "message" => "Error de edición: ID de recurso no recibido."]);
    exit;
}

$errors = validateProductData($recurso);

// Si hay errores de validación, devolver la respuesta con los detalles
if (!empty($errors)) {
    echo json_encode([
        "status" => "validation_error",
        "message" => "Errores de validación encontrados.",
        "details" => $errors
    ]);
    exit;
}

// Obtener datos 
$nombre_recurso = $recurso['nombre_recusro'] ?? '';
$autor = $recurso['autor'] ?? '';
$departamento = $recurso['departamento'] ?? '';
$empresa = $recurso['empresa'] ?? '';
$fecha_creacion = $recurso['fecha_creacion'] ?? '0000-00-00';
$descripcion = $recurso['descripcion'] ?? '';
$archivo = $recurso['archivo'] ?? ''; 
$logo = $recurso['logo'] ?? ''; 

$DEFAULT_IMAGE = 'img/default.png';

// Imagen (Aplicar default si vacío, se hace aquí para evitar que entre en validación de errores)
if (empty($imagen)) {
    $imagen = $DEFAULT_IMAGE;
}

$id_sql = mysqli_real_escape_string($conexion, $id);
$nombre_recurso_sql = mysqli_real_escape_string($conexion, $nombre_recurso);
$autor_sql = mysqli_real_escape_string($conexion, $autor);
$departamento_ql = mysqli_real_escape_string($conexion, $departamento);
$empresa_sql = mysqli_real_escape_string($conexion, $empresa);
$fecha_creacion_sql = mysqli_real_escape_string($conexion, $fecha_creacion);
$descripcion_sql = mysqli_real_escape_string($conexion, $descripcion);
$archivo_sql = mysqli_real_escape_string($conexion, $archivo);


$query = "UPDATE resourcesbd SET
            nombre_recurso = '$nombre_recurso_sql',
            autor = '$autor_sql',
            departamento = '$departamento_sql',
            empresa = $empresa_sql,
            fecha_creacion = $fecha_creacion_sql,
            descripcion = '$descripcion_sql',
            archivo = '$archivo_sql'
            logo = '$imagen';
          WHERE id = '$id_sql'";

$result = mysqli_query($conexion, $query);

// Devolver respuesta según el resultado de la consulta
if ($result) {
    if (mysqli_affected_rows($conexion) > 0) {
        echo json_encode(["status" => "success", "message" => "Recurso actualizado correctamente."]);
    } else {
        echo json_encode(["status" => "info", "message" => "Error no se realizaron cambios."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error al actualizar en BD " . mysqli_error($conexion)]);
}

mysqli_close($conexion);
?>