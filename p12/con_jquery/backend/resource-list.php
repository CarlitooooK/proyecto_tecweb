<?php
include_once __DIR__.'/database.php';

header('Content-Type: application/json; charset=utf-8');

// SE CREA EL ARREGLO QUE SE VA A DEVOLVER EN FORMA DE JSON
$data = array(
    'status' => 'error',
    'message' => '',
    'resources' => []
);

// SE REALIZA LA QUERY DE BÚSQUEDA
$sql = "SELECT * FROM resourcesbd WHERE eliminado = 0 ORDER BY fecha_creacion DESC";
$stmt = $conexion->prepare($sql);

if ($stmt) {
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $resources = array();
            while($row = $result->fetch_assoc()) {
                // Convertir valores null a string vacío
                foreach($row as $key => $value) {
                    $row[$key] = $value === null ? '' : $value;
                }
                $resources[] = $row;
            }
            
            $data['status'] = 'success';
            $data['resources'] = $resources;
            $data['count'] = count($resources);
        } else {
            $data['status'] = 'success';
            $data['message'] = 'No hay recursos registrados';
        }
        $result->free();
    } else {
        $data['message'] = 'Error al ejecutar la consulta: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $data['message'] = 'Error al preparar la consulta: ' . $conexion->error;
}

$conexion->close();

// SE HACE LA CONVERSIÓN DE ARRAY A JSON
echo json_encode($data, JSON_PRETTY_PRINT);
?>