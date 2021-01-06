<?php
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 0);
// error_reporting(0);

session_start();

$config = json_decode(file_get_contents(__DIR__ . '/config/config.json'), true);

function redirect($url='/') {
	header('Refresh: 0; url=' . $url);
}

function dbConnect() {
	global $config;
	$mysqli = mysqli_connect($config['db_hostname'], $config['db_username'], $config['db_password'], $config['db_name']);

	if (!$mysqli) return false;
	
	mysqli_query($mysqli, 'SET NAMES UTF8');

	return $mysqli;
}

$mysqli = dbConnect();

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

// CONST:
$weekdays = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье');
$months = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
$months_genetive = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');

?>