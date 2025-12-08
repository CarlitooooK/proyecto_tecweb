<?php
namespace App;

use PDO;

class Usuario {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function registrar($nombre, $email, $password) {
        $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$nombre, $email, $hashedPassword])) {
            return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al registrar usuario'];
    }
    
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['rol'] = $usuario['rol'];
            
            return ['success' => true, 'rol' => $usuario['rol']];
        }
        
        return ['success' => false, 'message' => 'Credenciales incorrectas'];
    }
    
    public function obtenerTodos() {
        $stmt = $this->db->query("SELECT id, nombre, email, rol, fecha_registro, activo FROM usuarios ORDER BY fecha_registro DESC");
        return $stmt->fetchAll();
    }
}
?>
