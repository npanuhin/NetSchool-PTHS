<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

if (!$AUTHORIZED) {
	logout();
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

if (!isset($_COOKIE['dark']) || $_COOKIE['dark'] == '0') {
	setcookie('dark', '1', time() + 60 * 60 * 24 * 365 * 100, '/; samesite=Lax', $httponly=false);
	echo '1';

} else {
	setcookie('dark', '0', time() + 60 * 60 * 24 * 365 * 100, '/; samesite=Lax', $httponly=false);
	echo '0';
}
?>