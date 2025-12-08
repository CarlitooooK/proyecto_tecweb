<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'src/Recurso.php';

requireLogin();

use App\Recurso;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
        $error = 'Error: Sesión de usuario no válida. Por favor, cierre sesión y vuelva a iniciar.';
    } elseif (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $datos = [
            'nombre_recurso' => $_POST['nombre_recurso'],
            'autor' => $_POST['autor'],
            'departamento' => $_POST['departamento'],
            'empresa_institucion' => $_POST['empresa_institucion'],
            'fecha_creacion' => $_POST['fecha_creacion'],
            'descripcion' => $_POST['descripcion'],
            'tipo_recurso' => $_POST['tipo_recurso'],
            'lenguaje_programacion' => $_POST['lenguaje_programacion'] ?? null
        ];
        
        $db = Database::getInstance()->getConnection();
        $recurso = new Recurso($db);
        $resultado = $recurso->crear($datos, $_FILES['archivo']);
        
        if ($resultado['success']) {
            $success = $resultado['message'];
            registrarAcceso('Recurso creado: ' . $datos['nombre_recurso']);
        } else {
            $error = $resultado['message'];
        }
    } else {
        $error = 'Error al subir el archivo';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Recurso - Catálogo</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>📚 Catálogo de Recursos</h2>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="btn btn-outline">← Volver al Catálogo</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h1>Cargar Nuevo Recurso</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <a href="index.php">Ver en catálogo</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="formRecurso">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre_recurso">Nombre del Recurso *</label>
                        <input type="text" id="nombre_recurso" name="nombre_recurso" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="autor">Autor *</label>
                        <input type="text" id="autor" name="autor" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="departamento">Departamento</label>
                        <input type="text" id="departamento" name="departamento">
                    </div>
                    
                    <div class="form-group">
                        <label for="empresa_institucion">Empresa/Institución</label>
                        <input type="text" id="empresa_institucion" name="empresa_institucion">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo_recurso">Tipo de Recurso *</label>
                        <select id="tipo_recurso" name="tipo_recurso" required onchange="toggleLenguaje()">
                            <option value="">Seleccionar...</option>
                            <option value="documento">Documento</option>
                            <option value="codigo">Código</option>
                            <option value="imagen">Imagen</option>
                            <option value="video">Video</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="lenguajeGroup" style="display:none;">
                        <label for="lenguaje_programacion">Lenguaje de Programación</label>
                        <input type="text" id="lenguaje_programacion" name="lenguaje_programacion" placeholder="Ej: PHP, JavaScript, Python">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="fecha_creacion">Fecha de Creación *</label>
                    <input type="date" id="fecha_creacion" name="fecha_creacion" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="archivo">Archivo *</label>
                    <input type="file" id="archivo" name="archivo" required>
                    <small>Máximo 10MB. Formatos permitidos: PDF, ZIP, RAR, DOCX, JPG, PNG, MP4, etc.</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Subir Recurso</button>
                <a href="index.php" class="btn btn-outline">Cancelar</a>
            </form>
        </div>
    </div>

    <script>
        function toggleLenguaje() {
            const tipo = document.getElementById('tipo_recurso').value;
            const lenguajeGroup = document.getElementById('lenguajeGroup');
            lenguajeGroup.style.display = tipo === 'codigo' ? 'block' : 'none';
        }
        
        document.getElementById('fecha_creacion').valueAsDate = new Date();
    </script>
</body>
</html>
