<?php
include_once __DIR__.'/database.php';
include_once __DIR__.'/resource-validation.php';
include_once __DIR__.'/filetype-icon.php';

// SE OBTIENE LA INFORMACIÓN DEL RECURSO ENVIADA POR EL CLIENTE
$recurso = file_get_contents('php://input');
$data = [
    'status' => 'error',
    'message' => 'Error en la solicitud'
];

if(!empty($recurso)) {
    // Transformar JSON a array
    $jsonARR = json_decode($recurso, true);
    
    // Validar datos
    $errors = validateProductData($jsonARR);
    
    if (!empty($errors)) {
        $data = [
            "status" => "validation_error",
            "message" => "Errores de validación encontrados.",
            "details" => $errors
        ];
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    // Convertir a objeto para mantener tu estructura
    $jsonOBJ = json_decode($recurso);
    
    // Logo automático
    $logo = assignLogoByExtension($jsonOBJ->archivo ?? '');
    
    // Escapar valores para SQL
    $nombre = $conexion->real_escape_string($jsonOBJ->nombre_recurso);
    $autor = $conexion->real_escape_string($jsonOBJ->autor);
    $departamento = $conexion->real_escape_string($jsonOBJ->departamento);
    $empresa = $conexion->real_escape_string($jsonOBJ->empresa);
    $fecha_creacion = $conexion->real_escape_string($jsonOBJ->fecha_creacion);
    $descripcion = $conexion->real_escape_string($jsonOBJ->descripcion);
    $archivo = $conexion->real_escape_string($jsonOBJ->archivo);
    
    // Validar nombre duplicado
    $sqlCheck = "SELECT id FROM resourcesbd WHERE nombre_recurso='$nombre' AND eliminado=0";
    $result = $conexion->query($sqlCheck);
    
    if ($result && $result->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Ya existe un recurso con ese nombre"
        ]);
        exit;
    }
    
    // Insertar nuevo recurso
    $sql = "INSERT INTO resourcesbd VALUES (
        null, 
        '$nombre', 
        '$autor', 
        '$departamento', 
        '$empresa', 
        '$fecha_creacion', 
        '$descripcion', 
        '$archivo', 
        0, 
        '$logo'
    )";
    
    if($conexion->query($sql)){
        $data['status'] = "success";
        $data['message'] = "Recurso agregado";
    } else {
        $data['message'] = "ERROR: No se ejecutó $sql. " . $conexion->error;
    }
    
    if(isset($result) && $result) {
        $result->free();
    }
    $conexion->close();
}

echo json_encode($data, JSON_PRETTY_PRINT);
?>