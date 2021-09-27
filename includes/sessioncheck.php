<?php
session_name(CUSTOM_SESSIONNAME); // Key limmatcms_j210621af
session_start();

$sessionExpiry = SESSION_EXPIRY*60; 

if (isset($_SESSION['logged'])) {
// Check if session exists and Timestamp validation
	if ($_SESSION['logged'] != true || $_SESSION['login_timestamp']+$sessionExpiry < time() ){
		
		// If session doesn't exisrs than logout
		setcookie (session_name(), null, -1, '/');
		session_destroy();
		session_write_close();
		
		// redirect to login page
		header('Location: ../admin/index.php');
		exit;
	}
} else {
	header('Location: ../admin/index.php');
	exit;
}

session_regenerate_id(); // New ID if the old one was hacked
$_SESSION['login_timestamp'] = time(); // Timestamp update

?>