<?php
include_once __DIR__.'/database.php';

header('Content-Type: application/json; charset=utf-8');

// SE CREA EL ARREGLO QUE SE VA A DEVOLVER EN FORMA DE JSON
$data = array(
    'status' => 'error',
    'message' => '',
    'resources' => []
);

// SE VERIFICA HABER RECIBIDO EL TÉRMINO DE BÚSQUEDA
if(isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = trim($_GET['search']);
    
    // Si es numérico, buscar también por ID
    $searchById = is_numeric($searchTerm) ? "OR id = ?" : "";
    
    // Usar prepared statement para evitar SQL injection
    $sql = "SELECT * FROM resourcesbd 
            WHERE (nombre_recurso LIKE ? 
                   OR autor LIKE ? 
                   OR departamento LIKE ? 
                   OR descripcion LIKE ? 
                   OR empresa LIKE ?
                   $searchById)
            AND eliminado = 0
            ORDER BY nombre_recurso";
    
    $stmt = $conexion->prepare($sql);
    
    if ($stmt) {
        $searchPattern = "%$searchTerm%";
        
        if (is_numeric($searchTerm)) {
            $searchId = (int)$searchTerm;
            $stmt->bind_param("sssssi", 
                $searchPattern, $searchPattern, $searchPattern, 
                $searchPattern, $searchPattern, $searchId);
        } else {
            $stmt->bind_param("sssss", 
                $searchPattern, $searchPattern, $searchPattern, 
                $searchPattern, $searchPattern);
        }
        
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
                $data['message'] = 'No se encontraron recursos';
            }
            $result->free();
        } else {
            $data['message'] = 'Error al ejecutar la búsqueda: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $data['message'] = 'Error al preparar la consulta: ' . $conexion->error;
    }
} else {
    $data['message'] = 'Término de búsqueda no proporcionado';
}

$conexion->close();

// SE HACE LA CONVERSIÓN DE ARRAY A JSON
echo json_encode($data, JSON_PRETTY_PRINT);
?>