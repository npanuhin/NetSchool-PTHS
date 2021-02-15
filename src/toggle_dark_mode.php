<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST) || !isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

header('Content-Type: application/json');

if (!isset($_SESSION['dark']) || !$_SESSION['dark']) {
	$_SESSION['dark'] = true;
	echo '1';
} else {
	$_SESSION['dark'] = false;
	echo '0';
}
?>