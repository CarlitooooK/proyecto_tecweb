<?php
include_once __DIR__.'/database.php';

// SE CREA EL ARREGLO QUE SE VA A DEVOLVER EN FORMA DE JSON
$data = array(
    'status'  => 'error',
    'message' => 'Parámetros incorrectos'
);

// SE VERIFICA HABER RECIBIDO EL ID
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Validar que el recurso existe y no está ya eliminado
    $check_sql = "SELECT id FROM resourcesbd WHERE id = ? AND eliminado = 0";
    $check_stmt = $conexion->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if($check_stmt->num_rows === 0) {
        $data['message'] = "El recurso no existe o ya fue eliminado";
        $check_stmt->close();
        $conexion->close();
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    $check_stmt->close();
    
    // Usar prepared statement para eliminar
    $sql = "UPDATE resourcesbd SET eliminado = 1 WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $data['status'] = "success";
            $data['message'] = "Recurso eliminado correctamente";
        } else {
            $data['message'] = "No se pudo eliminar el recurso";
        }
    } else {
        $data['message'] = "Error en la consulta: " . $conexion->error;
    }
    
    $stmt->close();
    $conexion->close();
} 

// SE HACE LA CONVERSIÓN DE ARRAY A JSON
echo json_encode($data, JSON_PRETTY_PRINT);
?>