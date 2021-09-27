<?php
require_once('config.php');
require_once('mysqli-connect.php');

session_name(CUSTOM_SESSIONNAME); // Key limmatcms_j210621af
session_start(); 

// Array overwrite (delete) 

$_SESSION = array();

// Cookies delete

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Session delete

session_destroy();

header('Location: ../index.php');
?>