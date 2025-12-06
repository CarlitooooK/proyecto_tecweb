<?php
function validateProductData(array $recurso): array {
    
    $nombre_recurso = trim($recurso['nombre_recurso'] ?? '');
    $autor = trim($recurso['autor'] ?? '');
    $departamento = trim($recurso['departamento'] ?? '');
    $empresa = $recurso['empresa'] ?? '';
    $fecha_creacion = $recurso['fecha_creacion'] ?? '';
    $descripcion = trim($recurso['descripcion'] ?? '');
    $archivo = trim($recurso['archivo'] ?? '');
    $logo = trim($recurso['logo'] ?? ''); 

    $errors = [];

    // 1. Nombre del recurso (Obligatorio, máx 250, alfanumérico)
    if (empty($nombre_recurso) || strlen($nombre_recurso) > 250 || !preg_match("/^[A-Za-z0-9\s\-]+$/", $departamento)) {
        $errors[] = "Nombre del recurso: requerido, alfanumérico (espacios/guiones) y máximo 250 caracteres.";
    }

    // 2. Autor (Obligatorio, máx 150)
    if (empty($autor) || strlen($autor) > 150) {
        $errors[] = "Autor: máximo 150 caracteres.";
    }

    // 3. Departamento (Opcional, máx 100, alfanumérico)
    if (strlen($departamento) > 100) {
        $errors[] = "Departamento: máximo 100 caracteres.";
    }

    // 4.Empresa (Opcional, máx 100)
    if (strlen($empresa) > 100) {
        $errors[] = "Empresa: máximo 100 caracteres.";
    }

    // 5. Descripcion (Opcional, máx 250)
    if (strlen($descripcion) > 250) {
        $errors[] = "Detalles: máximo 250 caracteres.";
    }

    // 6. Validar formato fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_creacion)) {
        $errors[] = "Fecha debe tener formato YYYY-MM-DD.";
    } else if (!strtotime($fecha_creacion)) {
        $errors[] = "La fecha no es válida.";
    }

    // 7. Archivo 
    /*if (empty($archivo)) {
        $errors[] = "Archivo: requerido";
    }*/

    return $errors;
}
?>