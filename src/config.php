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
$weekdays_short = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
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

$_short_lesson_name = [
	'Экспериментальная физика' => 'Эксп. физика'
];

$_task_types = [
	'А' => 'Практическая работа',
	'В' => 'Срезовая работа',
	'Д' => 'Домашняя работа',
	'К' => 'Контрольная работа',
	'С' => 'Самостоятельная работа',
	'Л' => 'Лабораторная работа',
	'П' => 'Проект',
	'Н' => 'Диктант',
	'Р' => 'Реферат',
	'О' => 'Ответ на уроке',
	'Ч' => 'Сочинение',
	'И' => 'Изложение',
	'З' => 'Зачёт',
	'Т' => 'Тестирование',
	'ДЗ' => 'Домашнее задание'
];

$profile_photos = [
	'Лось-Суницкая А. А.' => '/files/profile/Anna Anatolyevna.jpg',
];

$profile_colors = [
	'#0074D9',
	'#7FDBFF',
	'#39CCCC'
];

$_replace_class = [
	'/[АаAa]/u' => '[АаAa]',
	'/[Ббb]/u' => '[Ббb]',
	'/[ВвB]/u' => '[ВвB]'
];

$disabled_task_data_keys = [
	'Дата урока',
	'Срок сдачи',
	'Домашнее задание',
	'Тема задания'
];

$TODAY = new DateTime('today');
$TOMORROW = new DateTime('tomorrow');
$NOW = new DateTime('now');
$MONDAY = new DateTime('monday this week');
$SUNDAY = new DateTime('sunday this week');

$SCHOOL_YEAR = $TODAY->format('Y');
if ($TODAY->format('m') < 9) --$SCHOOL_YEAR;

$SCHOOL_YEAR_BEGIN = new DateTime($SCHOOL_YEAR . '-09-01');
$SCHOOL_YEAR_END = new DateTime(($SCHOOL_YEAR + 1) . '-05-31');

$TRUE_SCHOOL_YEAR_BEGIN = new DateTime($SCHOOL_YEAR_BEGIN->format('Y-m-d') . ' monday this week');
$TRUE_SCHOOL_YEAR_END = new DateTime($SCHOOL_YEAR_END->format('Y-m-d') . ' monday next week');

$SCHOOL_DAY_BORDER = new DateTime('15:00');
$SCHOOL_DAY = $NOW < (new DateTime($TODAY->format('Y-m-d') . ' ' . $SCHOOL_DAY_BORDER->format('H:i'))) ? $TODAY : $TOMORROW;

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
	// if (isset($_SESSION['HTTP_USER_AGENT'])) {
	// 	if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) return false;
	// } else {
	// 	$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	// }
	return true;
}

function logout() {
	$_SESSION = [];
	session_destroy();
}

function handle_lesson_name($string) {
	global $_lesson_name_regex;
	global $_lesson_name_replace;

	if (preg_match($_lesson_name_regex, $string, $matches)) $string = $matches[1];

	return $_lesson_name_replace[$string] ?? $string;
}

function short_lesson_name($lesson_name) {
	global $_short_lesson_name;
	return array_key_exists($lesson_name, $_short_lesson_name) ? $_short_lesson_name[$lesson_name] : $lesson_name;
}

function handle_task_type($task_type) {
	global $_task_types;
	return array_key_exists($task_type, $_task_types) ? $_task_types[$task_type] : $task_type;
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

function set_diary_period($db, $period_start, $period_end) {
	try {
		$data = $db->query('UPDATE `users` SET `diary_period_start` = ?s, `diary_period_end` = ?s WHERE `id` = ?i', $period_start, $period_end, $_SESSION['user_id']);

	} catch (Exception $e) {
		// print_r($e);
		exit(json_encode(array('message', 'Database request failed')));
	}
}

function class_to_diary_period($class) {
	global $TRUE_SCHOOL_YEAR_BEGIN;
	global $TRUE_SCHOOL_YEAR_END;

	return array($TRUE_SCHOOL_YEAR_BEGIN, $TRUE_SCHOOL_YEAR_END);
}


// CODE:
session_start();
?>