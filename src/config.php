<?php
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 0);
// error_reporting(0);

// MODULES:
require_once __DIR__ . '/safemysql.class.php';


// CONFIG:
$config = json_decode(file_get_contents(__DIR__ . '/config/config.json'), true);


// CONST:
$weekdays = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
$months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
$months_genetive = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];

$_lesson_name_regex = '/^\s*(.+?)(?:[\\\\\/]?базовый)?\s*$/imu';

$_lesson_name_replace = [
	'Информ.' => 'Информатика',
	'Эксп.физика' => 'Экспериментальная физика',
	'Физкульт.' => 'Физкультура',
	'Рус.язык' => 'Русский язык',
	'Англ.язык' => 'Английский язык',
	'Нем.язык' => 'Немецкий язык'
];

$profile_photos = [
	'Лось-Суницкая А. А.' => '/files/profile/Anna Anatolyevna.jpg',
];

$_replace_class = [
	'/[АаAa]/u' => '[АаAa]',
	'/[Ббb]/u' => '[Ббb]',
	'/[ВвB]/u' => '[ВвB]'
];


// UTILS:
function redirect($url='/') {
	header('Refresh: 0; url=' . $url);
}

function dbConnect() {
	global $config;
	
	return new SafeMysql(array(
		'host' => $config['db_hostname'],
		'user' => $config['db_username'],
		'pass' => $config['db_password'],
		'db' => $config['db_name'],
		'charset' => 'utf8'
	));
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

function handle_lesson_name($string) {
	global $_lesson_name_regex;
	global $_lesson_name_replace;

	if (preg_match($_lesson_name_regex, $string, $matches)) {
		$string = $matches[1];
	}

	return $_lesson_name_replace[$string] ?? $string;
}

function replace_school_class_regex($school_class) {
	global $_replace_class;

	if (preg_match('/(\d+)(\w)\w*/iu', $school_class, $matches)) {
		$class = $matches[2];
		foreach ($_replace_class as $key => $value) {
			$class = preg_replace($key, $value, $class);
		}
		$school_class = $matches[1] . $class;
	}

	return $school_class;
}


// CODE:
session_start();
?>