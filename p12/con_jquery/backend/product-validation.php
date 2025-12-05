<?php
function validateProductData(array $recurso): array {
    
    $nombre_recurso = trim($recurso['nombre_recurso'] ?? '');
    $autor = trim($recurso['autor'] ?? '');
    $departamento = trim($recurso['departamento'] ?? '');
    $empresa = $recurso['empresa'] ?? 0.0;
    $fecha_creacion = $recurso['fecha_creacion'] ?? 0;
    $descripcion = trim($recurso['descripcion'] ?? '');
    $archivo = trim($recurso['archivo'] ?? '');
    $logo = trim($recurso['logo'] ?? ''); 

    $errors = [];

    // 1. Nombre (Requerido, máx 100)
    if (empty($nombre_recurso) || strlen($nombre_recurso) > 250) {
        $errors[] = "Nombre del recurso: requerido y máximo 250 caracteres.";
    }

    // 2. Marca (Requerida)
    if (empty($autor) || strlen($autor) > 150) {
        $errors[] = "Autor: requerido y máximo 100 caracteres.";
    }

    // 3. Modelo (Requerido, alfanumérico, máx 25)
    if (empty($modelo) || strlen($modelo) > 25 || !preg_match("/^[A-Za-z0-9\s\-]+$/", $modelo)) {
        $errors[] = "Modelo: requerido, alfanumérico (espacios/guiones) y máximo 25 caracteres.";
    }

    // 4. Precio (Requerido, mayor a 99.99)
    $precio_float = floatval($precio);
    if ($precio_float <= 99.99) {
        $errors[] = "Precio: debe ser mayor a 99.99.";
    }

    // 5. Unidades (Requeridas, >= 0)
    $unidades_int = intval($unidades);
    if ($unidades_int < 0) {
        $errors[] = "Unidades: debe ser un número mayor o igual a 0.";
    }

    // 6. Detalles (Opcional, máx 250)
    if (strlen($detalles) > 250) {
        $errors[] = "Detalles: máximo 250 caracteres.";
    }

    return $errors;
}
?>