<?php
function validateProductData(array $recurso): array {
    
    $nombre_recurso = trim($recurso['nombre_recurso'] ?? '');
    $autor = trim($recurso['autor'] ?? '');
    $departamento = trim($recurso['departamento'] ?? '');
    $empresa = trim($recurso['empresa'] ?? '');
    $fecha_creacion = $recurso['fecha_creacion'] ?? '';
    $descripcion = trim($recurso['descripcion'] ?? '');
    $archivo = trim($recurso['archivo'] ?? '');

    $errors = [];

    // 1. Nombre del recurso
    if (empty($nombre_recurso)) {
        $errors[] = "Nombre del recurso: requerido.";
    } elseif (strlen($nombre_recurso) > 250) {
        $errors[] = "Nombre del recurso: máximo 250 caracteres.";
    }

    // 2. Autor
    if (empty($autor)) {
        $errors[] = "Autor: requerido.";
    } elseif (strlen($autor) > 150) {
        $errors[] = "Autor: máximo 150 caracteres.";
    }

    // 3. Departamento
    if (strlen($departamento) > 100) {
        $errors[] = "Departamento: máximo 100 caracteres.";
    }

    // 4. Empresa
    if (strlen($empresa) > 100) {
        $errors[] = "Empresa: máximo 100 caracteres.";
    }

    // 5. Descripción
    if (strlen($descripcion) > 250) {
        $errors[] = "Descripción: máximo 250 caracteres.";
    }

    // 6. Fecha
    if (empty($fecha_creacion)) {
        $errors[] = "Fecha: requerida.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_creacion)) {
        $errors[] = "Fecha: debe tener formato YYYY-MM-DD.";
    } else {
        $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha_creacion);
        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha_creacion) {
            $errors[] = "Fecha: no es una fecha válida.";
        }
    }

    return $errors;
}
?>