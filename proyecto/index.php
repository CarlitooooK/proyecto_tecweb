<?php

session_start();

if (isset($_SESSION["user_id"])) {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = "SELECT * FROM user
            WHERE id = {$_SESSION["user_id"]}";
            
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Inicio</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <?php if (isset($user)): ?>
        <div class="home-container">
            <h1>¡Bienvenido!</h1>
            
            <div class="welcome-text">
                <p>Hola <span class="user-name"><?= htmlspecialchars($user["name"]) ?></span></p>
                <p>Has iniciado sesión correctamente.</p>
            </div>
            
            <div class="nav-buttons">
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    <?php else: ?>
        <div class="home-container">
            <h1>Bienvenido</h1>
            
            <div class="welcome-text">
                <p>Por favor, inicia sesión o crea una cuenta para continuar.</p>
            </div>
            
            <div class="nav-buttons">
                <a href="login.php">Iniciar Sesión</a>
                <a href="signup.html">Registrarse</a>
            </div>
        </div>
    <?php endif; ?>
    
</body>
</html>










