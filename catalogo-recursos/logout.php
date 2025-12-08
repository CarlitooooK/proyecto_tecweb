<?php
require_once 'config/session.php';

registrarAcceso('Logout');

session_destroy();
header('Location: index.php');
exit();
?>
