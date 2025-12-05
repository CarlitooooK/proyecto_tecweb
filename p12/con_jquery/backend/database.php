<?php
    $conexion = @mysqli_connect(
        'localhost',
        'root',
        'seguro',
        'resourcesbd'
    );

    /**
     * NOTA: si la conexión falló $connection contendrá false
     **/

    if (!$conexion) { // Usar $connection aquí también
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Base de datos NO conectada: " . mysqli_connect_error()
        ]);
            exit;
    }
   
?>