<?php
    include_once __DIR__.'/database.php';
    include_once __DIR__.'/product-validation.php'; // Incluimos la función de validación

    // SE OBTIENE LA INFORMACIÓN DEL PRODUCTO ENVIADA POR EL CLIENTE
    $recurso = file_get_contents('php://input');
    $data = array(
        'status'  => 'error',
        'message' => 'Ya existe un recurso con ese nombre'
    );
    if(!empty($recurso)) {
        // SE TRANSFORMA EL STRING DEL JSON A ARREGLO ASOCIATIVO para la validación (se agrega 'true')
        $jsonARR = json_decode($recurso, true);
        
        $errors = validateProductData($jsonARR);

        // Si hay errores de validación, devolver la respuesta con los detalles y salir
        if (!empty($errors)) {
            $data = [
                "status" => "validation_error",
                "message" => "Errores de validación encontrados.",
                "details" => $errors
            ];
            // Se devuelve la respuesta de error de validación y se salta el resto del script
            echo json_encode($data, JSON_PRETTY_PRINT);
            exit; 
        }

        // Si no hay errores, se transforma de nuevo a objeto (o se usa el arreglo, pero mantendré tu estructura)
        $jsonOBJ = json_decode($recurso);
        
        // SE ASUME QUE LOS DATOS YA FUERON VALIDADOS ANTES DE ENVIARSE
        $sql = "SELECT * FROM resourcesbd WHERE nombre_recurso = '{$jsonOBJ->nombre_recurso}' AND eliminado = 0";
	    $result = $conexion->query($sql);
        
        if ($result->num_rows == 0) {
            $conexion->set_charset("utf8");
            $sql = "INSERT INTO resourcesbd VALUES (null, '{$jsonOBJ->nombre_recurso}', '{$jsonOBJ->autor}', '{$jsonOBJ->departamento}', '{$jsonOBJ->empresa}', {$jsonOBJ->fecha_creacion}, '{$jsonOBJ->descripcion}', {$jsonOBJ->archivo}, 0, '{$jsonOBJ->logo}')";
            if($conexion->query($sql)){
                $data['status'] =  "success";
                $data['message'] =  "Recurso agregado";
            } else {
                $data['message'] = "ERROR: No se ejecuto $sql. " . mysqli_error($conexion);
            }
        }

        $result->free();
        // Cierra la conexion
        $conexion->close();
    }

    // SE HACE LA CONVERSIÓN DE ARRAY A JSON
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
?>