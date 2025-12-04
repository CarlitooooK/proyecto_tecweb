<?php

function showError($message) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Error de Registro</title>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <div class='container'>
            <h1>Error en el Registro</h1>
            <div class='error-message'>$message</div>
            <div class='link'>
                <a href='signup.html'>Volver al registro</a>
            </div>
        </div>
    </body>
    </html>";
    exit;
}

if (empty($_POST["name"])) {
    showError("El nombre es requerido");
}

if ( ! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    showError("Se requiere un email válido");
}

if (strlen($_POST["password"]) < 8) {
    showError("La contraseña debe tener al menos 8 caracteres");
}

if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
    showError("La contraseña debe contener al menos una letra");
}

if ( ! preg_match("/[0-9]/", $_POST["password"])) {
    showError("La contraseña debe contener al menos un número");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    showError("Las contraseñas no coinciden");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

$sql = "INSERT INTO user (name, email, password_hash)
        VALUES (?, ?, ?)";
        
$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sss",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash);
                  
if ($stmt->execute()) {
    header("Location: signup-success.html");
    exit;
} else {
    if ($mysqli->errno === 1062) {
        showError("Este email ya está registrado");
    } else {
        showError("Error en el servidor: " . $mysqli->error);
    }
}








