<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'src/Recurso.php';

requireLogin();
use App\Recurso;

$error = '';
$success = '';

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: ID no proporcionado.");
}

$id = intval($_GET['id']);

$db = Database::getInstance()->getConnection();
$recursoObj = new Recurso($db);

// Obtener datos actuales
$recurso = $recursoObj->obtenerPorId($id);

if (!$recurso) {
    die("Error: Recurso no encontrado.");
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
        $error = 'Error de sesión. Cierre sesión e inicie nuevamente.';
    } else {
        $datos = [
            'id' => $id,
            'nombre_recurso' => $_POST['nombre_recurso'],
            'autor' => $_POST['autor'],
            'departamento' => $_POST['departamento'],
            'empresa_institucion' => $_POST['empresa_institucion'],
            'fecha_creacion' => $_POST['fecha_creacion'],
            'descripcion' => $_POST['descripcion'],
            'tipo_recurso' => $_POST['tipo_recurso'],
            'lenguaje_programacion' => $_POST['lenguaje_programacion'] ?? null
        ];

        $archivoNuevo = null;

        // Si el usuario quiere reemplazar archivo
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $archivoNuevo = $_FILES['archivo'];
        }

        $resultado = $recursoObj->actualizar($datos, $archivoNuevo);

        if ($resultado['success']) {
            $success = "Recurso actualizado correctamente.";
        } else {
            $error = $resultado['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Recurso</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <div class="nav-brand">
            <h2>✏️ Editar Recurso</h2>
        </div>
        <div class="nav-menu">
            <a href="index.php" class="btn btn-outline">← Volver al Catálogo</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-container">

        <h1>Modificar Recurso</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
                <a href="index.php">Ver en catálogo</a>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-row">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre_recurso" required 
                        value="<?= htmlspecialchars($recurso['nombre_recurso']) ?>">
                </div>

                <div class="form-group">
                    <label>Autor *</label>
                    <input type="text" name="autor" required 
                        value="<?= htmlspecialchars($recurso['autor']) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Departamento</label>
                    <input type="text" name="departamento"
                        value="<?= htmlspecialchars($recurso['departamento']) ?>">
                </div>

                <div class="form-group">
                    <label>Empresa / Institución</label>
                    <input type="text" name="empresa_institucion"
                        value="<?= htmlspecialchars($recurso['empresa_institucion']) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipo *</label>
                    <select name="tipo_recurso" required onchange="toggleLenguaje()">
                        <option value="documento" <?= $recurso['tipo_recurso']=='documento'?'selected':'' ?>>Documento</option>
                        <option value="codigo" <?= $recurso['tipo_recurso']=='codigo'?'selected':'' ?>>Código</option>
                        <option value="imagen" <?= $recurso['tipo_recurso']=='imagen'?'selected':'' ?>>Imagen</option>
                        <option value="video" <?= $recurso['tipo_recurso']=='video'?'selected':'' ?>>Video</option>
                        <option value="otro" <?= $recurso['tipo_recurso']=='otro'?'selected':'' ?>>Otro</option>
                    </select>
                </div>

                <div class="form-group" id="lenguajeGroup" style="<?= $recurso['tipo_recurso']=='codigo' ? '' : 'display:none;' ?>">
                    <label>Lenguaje de Programación</label>
                    <input type="text" name="lenguaje_programacion"
                        value="<?= htmlspecialchars($recurso['lenguaje_programacion']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Fecha *</label>
                <input type="date" name="fecha_creacion" required 
                    value="<?= $recurso['fecha_creacion'] ?>">
            </div>

            <div class="form-group">
                <label>Descripción *</label>
                <textarea name="descripcion" rows="4" required><?= htmlspecialchars($recurso['descripcion']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Archivo actual:</label><br>
                <a href="<?= $recurso['ruta_archivo'] ?>" target="_blank" class="btn btn-outline">Ver archivo</a>
            </div>

            <div class="form-group">
                <label>Reemplazar archivo (opcional)</label>
                <input type="file" name="archivo">
                <small>Máximo 10MB. Solo si deseas reemplazar el existente.</small>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="index.php" class="btn btn-outline">Cancelar</a>

        </form>
    </div>
</div>

<script>
function toggleLenguaje() {
    const tipo = document.querySelector("[name='tipo_recurso']").value;
    document.getElementById('lenguajeGroup').style.display = (tipo === 'codigo') ? 'block' : 'none';
}
</script>

</body>
</html>
