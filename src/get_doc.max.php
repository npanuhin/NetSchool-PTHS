<?php
session_start();

if (
	$_SERVER['REQUEST_METHOD'] != 'GET'  || !isset($_GET)         ||
	!isset($_SESSION['user_id'])         || !$_SESSION['user_id'] ||
	!isset($_GET['file'])                || !trim($_GET['file'])  ||
	!file_exists('../doc/' . trim($_GET['file']))
) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . trim($_GET['file']));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize('../doc/' . trim($_GET['file'])));

readfile('../doc/' . trim($_GET['file']));
?>