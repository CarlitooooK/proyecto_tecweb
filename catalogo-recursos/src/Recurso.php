<?php
namespace App;

use PDO;

class Recurso {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function crear($datos, $archivo) {
        if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
            return ['success' => false, 'message' => 'Debe iniciar sesión para crear recursos'];
        }
        
        $uploadDir = __DIR__ . '/../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $nombreArchivo = time() . '_' . basename($archivo['name']);
        $rutaDestino = $uploadDir . $nombreArchivo;
        
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $checkUser = $this->db->prepare("SELECT id FROM usuarios WHERE id = ?");
            $checkUser->execute([$_SESSION['usuario_id']]);
            
            if (!$checkUser->fetch()) {
                unlink($rutaDestino); // Eliminar archivo subido
                return ['success' => false, 'message' => 'Usuario no válido. Por favor, inicie sesión nuevamente.'];
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO recursos 
                (nombre_recurso, autor, departamento, empresa_institucion, fecha_creacion, 
                descripcion, archivo_ruta, archivo_original, tipo_recurso, lenguaje_programacion, usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $resultado = $stmt->execute([
                $datos['nombre_recurso'],
                $datos['autor'],
                $datos['departamento'],
                $datos['empresa_institucion'],
                $datos['fecha_creacion'],
                $datos['descripcion'],
                'uploads/' . $nombreArchivo,
                $archivo['name'],
                $datos['tipo_recurso'],
                $datos['lenguaje_programacion'] ?? null,
                $_SESSION['usuario_id']
            ]);
            
            return ['success' => $resultado, 'message' => $resultado ? 'Recurso creado exitosamente' : 'Error al crear recurso'];
        }
        
        return ['success' => false, 'message' => 'Error al subir el archivo'];
    }
    
    public function obtenerTodos($incluirEliminados = false) {
        $sql = "SELECT r.*, u.nombre as usuario_nombre 
                FROM recursos r 
                LEFT JOIN usuarios u ON r.usuario_id = u.id";
        
        if (!$incluirEliminados) {
            $sql .= " WHERE r.eliminado = 0";
        }
        
        $sql .= " ORDER BY r.fecha_subida DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM recursos WHERE id = ? AND eliminado = 0");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function actualizar($datos, $archivoNuevo = null) {
        // Verificar sesión
        if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
            return ['success' => false, 'message' => 'Debe iniciar sesión para modificar recursos'];
        }

        // Obtener datos actuales para saber si hay archivo previo
        $stmt = $this->db->prepare("SELECT archivo_ruta FROM recursos WHERE id = ?");
        $stmt->execute([$datos['id']]);
        $actual = $stmt->fetch();

        if (!$actual) {
            return ['success' => false, 'message' => 'Recurso no encontrado'];
        }

        $archivoRutaFinal = $actual['archivo_ruta'];   
        $archivoOriginal = null;

        // Si el usuario sube un nuevo archivo, reemplazarlo
        if ($archivoNuevo && $archivoNuevo['error'] === UPLOAD_ERR_OK) {

            // Carpeta uploads
            $uploadDir = __DIR__ . '/../uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Nombre nuevo
            $nuevoNombre = time() . '_' . basename($archivoNuevo['name']);
            $rutaDestino = $uploadDir . $nuevoNombre;

            // Guardar nuevo archivo
            if (move_uploaded_file($archivoNuevo['tmp_name'], $rutaDestino)) {
                
                // Eliminar archivo anterior si existe
                $rutaVieja = __DIR__ . '/../' . $actual['archivo_ruta'];
                if (file_exists($rutaVieja)) {
                    unlink($rutaVieja);
                }

                // Guardar nuevo nombre
                $archivoRutaFinal = 'uploads/' . $nuevoNombre;
                $archivoOriginal = $archivoNuevo['name'];

            } else {
                return ['success' => false, 'message' => 'Error al reemplazar archivo'];
            }
        }

        // Actualizar campos
        $stmt = $this->db->prepare("
            UPDATE recursos 
            SET nombre_recurso = ?, 
                autor = ?, 
                departamento = ?, 
                empresa_institucion = ?, 
                fecha_creacion = ?, 
                descripcion = ?, 
                tipo_recurso = ?, 
                lenguaje_programacion = ?, 
                archivo_ruta = ?, 
                archivo_original = ?
            WHERE id = ?
        ");

        $resultado = $stmt->execute([
            $datos['nombre_recurso'],
            $datos['autor'],
            $datos['departamento'],
            $datos['empresa_institucion'],
            $datos['fecha_creacion'],
            $datos['descripcion'],
            $datos['tipo_recurso'],
            $datos['lenguaje_programacion'] ?? null,
            $archivoRutaFinal,
            $archivoOriginal ?? $actual['archivo_ruta'], 
            $datos['id']
        ]);

        return [
            'success' => $resultado,
            'message' => $resultado ? 'Recurso actualizado correctamente' : 'Error al actualizar recurso'
        ];
    }
    
    public function eliminar($id) {
        $stmt = $this->db->prepare("UPDATE recursos SET eliminado = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function registrarDescarga($recursoId) {
        $diaSemana = date('l');
        $horaDia = (int)date('G');
        
        $stmt = $this->db->prepare("
            INSERT INTO bitacora_descargas 
            (recurso_id, usuario_id, ip_address, dia_semana, hora_dia) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $recursoId,
            $_SESSION['usuario_id'] ?? null,
            $_SERVER['REMOTE_ADDR'],
            $diaSemana,
            $horaDia
        ]);
    }
}
?>
