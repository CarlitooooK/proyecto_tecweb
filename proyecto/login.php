<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = sprintf("SELECT * FROM user
                    WHERE email = '%s'",
                   $mysqli->real_escape_string($_POST["email"]));
    
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
    
    if ($user) {
        
        if (password_verify($_POST["password"], $user["password_hash"])) {
            
            session_start();
            
            session_regenerate_id();
            
            $_SESSION["user_id"] = $user["id"];
            
            header("Location: index.php");
            exit;
        }
    }
    
    $is_invalid = true;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <div class="container">
        <h1>Iniciar Sesión</h1>
        
        <?php if ($is_invalid): ?>
            <div class="error-message">
                <strong>Error:</strong> Email o contraseña incorrectos
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email"
                       value="<?= htmlspecialchars($_POST["email"] ?? "") ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
        
        <div class="link">
            <p>¿No tienes cuenta? <a href="signup.html">Regístrate aquí</a></p>
        </div>
    </div>
    
</body>
</html>








