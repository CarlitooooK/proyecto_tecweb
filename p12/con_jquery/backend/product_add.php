<?php
    include_once __DIR__.'/database.php';
    include_once __DIR__.'/product-validation.php'; // Incluimos la función de validación

    // SE OBTIENE LA INFORMACIÓN DEL PRODUCTO ENVIADA POR EL CLIENTE
    $producto = file_get_contents('php://input');
    $data = array(
        'status'  => 'error',
        'message' => 'Ya existe un producto con ese nombre'
    );
    if(!empty($producto)) {
        // SE TRANSFORMA EL STRING DEL JSON A ARREGLO ASOCIATIVO para la validación (se agrega 'true')
        $jsonARR = json_decode($producto, true);
        
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
        $jsonOBJ = json_decode($producto);
        
        // SE ASUME QUE LOS DATOS YA FUERON VALIDADOS ANTES DE ENVIARSE
        $sql = "SELECT * FROM productos WHERE nombre = '{$jsonOBJ->nombre}' AND eliminado = 0";
	    $result = $conexion->query($sql);
        
        if ($result->num_rows == 0) {
            $conexion->set_charset("utf8");
            $sql = "INSERT INTO productos VALUES (null, '{$jsonOBJ->nombre}', '{$jsonOBJ->marca}', '{$jsonOBJ->modelo}', {$jsonOBJ->precio}, '{$jsonOBJ->detalles}', {$jsonOBJ->unidades}, '{$jsonOBJ->imagen}', 0)";
            if($conexion->query($sql)){
                $data['status'] =  "success";
                $data['message'] =  "Producto agregado";
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