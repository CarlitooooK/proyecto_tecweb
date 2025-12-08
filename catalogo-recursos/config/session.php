<?php
require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function isAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /index.php');
        exit();
    }
}

function registrarAcceso($accion) {
    if (!isLoggedIn()) return;
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO bitacora_acceso (usuario_id, accion, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['usuario_id'],
        $accion,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}
?>
