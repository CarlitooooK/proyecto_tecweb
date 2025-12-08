<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'src/Usuario.php';

use App\Usuario;

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $db = Database::getInstance()->getConnection();
    $usuario = new Usuario($db);
    $resultado = $usuario->login($email, $password);
    
    if ($resultado['success']) {
        registrarAcceso('Login exitoso');
        header('Location: ' . ($resultado['rol'] === 'admin' ? 'dashboard.php' : 'index.php'));
        exit();
    } else {
        $error = $resultado['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Catálogo de Recursos</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Iniciar Sesión</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
            
            <p class="auth-footer">
                ¿No tienes cuenta? <a href="signup.php">Regístrate aquí</a>
            </p>
            
            <p class="auth-footer">
                <a href="index.php">← Volver al catálogo</a>
            </p>
        </div>
    </div>
</body>
</html>
