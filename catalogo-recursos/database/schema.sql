-- Crear base de datos
CREATE DATABASE IF NOT EXISTS catalogo_recursos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE catalogo_recursos;

-- Tabla de USUARIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de RECURSOS
CREATE TABLE IF NOT EXISTS recursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_recurso VARCHAR(200) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    departamento VARCHAR(100),
    empresa_institucion VARCHAR(150),
    fecha_creacion DATE NOT NULL,
    descripcion TEXT,
    archivo_ruta VARCHAR(255) NOT NULL,
    archivo_original VARCHAR(255) NOT NULL,
    tipo_recurso ENUM('documento', 'codigo', 'imagen', 'video', 'otro') DEFAULT 'documento',
    lenguaje_programacion VARCHAR(50),
    eliminado TINYINT(1) DEFAULT 0,
    usuario_id INT NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_eliminado (eliminado),
    INDEX idx_tipo (tipo_recurso),
    INDEX idx_lenguaje (lenguaje_programacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de BITÁCORA DE ACCESO
CREATE TABLE IF NOT EXISTS bitacora_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha_hora),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de BITÁCORA DE DESCARGAS
CREATE TABLE IF NOT EXISTS bitacora_descargas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recurso_id INT NOT NULL,
    usuario_id INT,
    ip_address VARCHAR(45),
    fecha_descarga TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    dia_semana VARCHAR(20),
    hora_dia INT,
    FOREIGN KEY (recurso_id) REFERENCES recursos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_recurso (recurso_id),
    INDEX idx_fecha (fecha_descarga),
    INDEX idx_dia (dia_semana),
    INDEX idx_hora (hora_dia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto (password: admin123)
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Administrador', 'admin@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertar datos de ejemplo
INSERT INTO recursos (nombre_recurso, autor, departamento, empresa_institucion, fecha_creacion, descripcion, archivo_ruta, archivo_original, tipo_recurso, lenguaje_programacion, usuario_id) VALUES
('Sistema de Gestión PHP', 'Juan Pérez', 'Desarrollo', 'TechCorp', '2024-01-15', 'Sistema completo de gestión de recursos desarrollado en PHP', 'uploads/sistema_gestion.zip', 'sistema_gestion.zip', 'codigo', 'PHP', 1),
('Manual de Usuario', 'María García', 'Documentación', 'TechCorp', '2024-02-20', 'Manual completo del sistema', 'uploads/manual_usuario.pdf', 'manual_usuario.pdf', 'documento', NULL, 1),
('API REST en Node.js', 'Carlos Rodríguez', 'Backend', 'DevSolutions', '2024-03-10', 'Implementación de API REST', 'uploads/api_nodejs.zip', 'api_nodejs.zip', 'codigo', 'JavaScript', 1);
