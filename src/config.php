<?php
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(0);

session_start();

$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

function redirect($url) {
	header('Refresh: 0; url=' . $url);
}

function verifySession() {
	if (isset($_SESSION['HTTP_USER_AGENT'])) {
		if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) return false;
	} else {
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	}
	return true;
}

function logout() {
	$_SESSION = [];
	session_destroy();
}
?>