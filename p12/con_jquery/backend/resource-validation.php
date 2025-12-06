<?php
function validateProductData(array $recurso): array {
    
    $nombre_recurso = trim($recurso['nombre_recurso'] ?? '');
    $autor = trim($recurso['autor'] ?? '');
    $departamento = trim($recurso['departamento'] ?? '');
    $empresa = $recurso['empresa'] ?? 0.0;
    $fecha_creacion = $recurso['fecha_creacion'] ?? '0000-00-00';
    $descripcion = trim($recurso['descripcion'] ?? '');
    $archivo = trim($recurso['archivo'] ?? '');
    $logo = trim($recurso['logo'] ?? ''); 

    $errors = [];

    // 1. Nombre del recurso
    if (empty($nombre_recurso) || strlen($nombre_recurso) > 250 || !preg_match("/^[A-Za-z0-9\s\-]+$/", $departamento)) {
        $errors[] = "Nombre del recurso: requerido, alfanumérico (espacios/guiones) y máximo 250 caracteres.";
    }

    // 2. Autor
    if (empty($autor) || strlen($autor) > 150) {
        $errors[] = "Autor: máximo 100 caracteres.";
    }

    // 3. Departamento
    if (strlen($departamento) > 100 || !preg_match("/^[A-Za-z0-9\s\-]+$/", $departamento)) {
        $errors[] = "Departamento: alfanumérico (espacios/guiones) y máximo 25 caracteres.";
    }

    // 4.Empresa
    if (strlen($empresa) > 100) {
        $errors[] = "Empresa: máximo 100 caracteres.";
    }

    // 5. Fecha de creacion
    if (empty($fecha_creacion) || strlen($fecha_creacion) > 100) {
        $errors[] = "Empresa: máximo 100 caracteres.";
    }

    // 6. Descripcion (Opcional, máx 250)
    if (strlen($descripcion) > 250) {
        $errors[] = "Detalles: máximo 250 caracteres.";
    }

    // 7. Archivo 
    if (empty($archivo)) {
        $errors[] = "Archivo: requerido";
    }

    return $errors;
}
?>