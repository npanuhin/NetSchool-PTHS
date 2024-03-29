<?php
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 0);
// error_reporting(0);

// session_start();

$db = null;
$UI_ERROR = null;


// ========================================== MODULES ==========================================

require_once __DIR__ . '/lib/safemysql.class.php';
require_once __DIR__ . '/lib/Telegram.php';


// ========================================== CONFIGS ==========================================

$config = json_decode(file_get_contents(__DIR__ . '/config/config.json'), true);

$telegram = new Telegram($config['telegram_token']);


// =========================================== CONST ===========================================

$weekdays = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
$weekdays_short = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
$months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
$months_genetive = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];

$_lesson_name_regex = '/^\s*(.+?)(?:[\\\\\/]?базовый)?\s*$/imu';
$_class_regex = '/(\d+)(\w)\w*/iu';
$_href_regex = "/(([a-z]+:\/\/)?(?:[a-zа-я0-9@:_-]+\.)+[a-zа-я0-9]{2,4}(?(2)|\/).*?)([-.,:]?(?:\\s|\$))/is";

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

$_possible_class_letters = 'АаAaБбbВвB';

$_replace_class_letter = [
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
$SCHOOL_COURSES_DAY_BORDER = new DateTime('18:30'); // some courses start at 18:00, and for thoses, who are late...
$SCHOOL_DAY = clone ($NOW < (new DateTime($TODAY->format('Y-m-d') . ' ' . $SCHOOL_DAY_BORDER->format('H:i'))) ? $TODAY : $TOMORROW);
$SCHOOL_COURSES_DAY = clone ($NOW < (new DateTime($TODAY->format('Y-m-d') . ' ' . $SCHOOL_COURSES_DAY_BORDER->format('H:i'))) ? $TODAY : $TOMORROW);

if($SCHOOL_DAY == $SUNDAY) $SCHOOL_DAY -> add(date_interval_create_from_date_string('1 day'));

if($SCHOOL_COURSES_DAY == $SUNDAY) $SCHOOL_COURSES_DAY -> add(date_interval_create_from_date_string('1 day'));

// =========================================== UTILS ===========================================

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

function logout() { // Before sending data (sending headers)
	// Unset cookies
	if (isset($_SERVER['HTTP_COOKIE'])) {
		$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		foreach ($cookies as $cookie) {
			$parts = explode('=', $cookie);
			$name = trim($parts[0]);
			setcookie($name, '', time() - 1000);
			setcookie($name, '', time() - 1000, '/');
		}
	}
	// session_destroy();
}

function person_hash($person) {
	return md5($person['id'] . $person['username'] . $person['password']);
}

function handle_lesson_name($string) {
	global $_lesson_name_regex, $_lesson_name_replace;

	if (preg_match($_lesson_name_regex, $string, $matches)) $string = $matches[1];

	return $_lesson_name_replace[$string] ?? $string;
}

function short_lesson_name($lesson_name) {
	global $_short_lesson_name;
	return isset($_short_lesson_name[$lesson_name]) ? $_short_lesson_name[$lesson_name] : $lesson_name;
}

function handle_task_type($task_type) {
	global $_task_types;
	return isset($_task_types[$task_type]) ? $_task_types[$task_type] : $task_type;
}

function get_school_class_regex($school_class) {
	global $_replace_class_letter, $_class_regex, $_possible_class_letters;

	if (preg_match($_class_regex, $school_class, $matches)) {
		$class = $matches[2];
		foreach ($_replace_class_letter as $key => $value) {
			$class = preg_replace($key, $value, $class);
		}
		if ($matches[1] && $class) $school_class = $matches[1] . '[' . $_possible_class_letters . ']*' . $class . '[' . $_possible_class_letters . ']*';
	}

	return $school_class;
}

function set_diary_period($db, $person, $period_start, $period_end) {
	try {
		$data = $db->query('UPDATE `users` SET `diary_period_start` = ?s, `diary_period_end` = ?s WHERE `id` = ?i', $period_start, $period_end, $person['id']);

	} catch (Exception $e) {
		// print_r($e);
		telegram_log("Database request failed\nUser ID: {$person['id']}\n\n" . $e->getMessage());
		exit(json_encode(array('message', 'Database request failed')));
	}
}

function class_to_diary_period($db, $class) {
	global $TRUE_SCHOOL_YEAR_BEGIN, $TRUE_SCHOOL_YEAR_END, $_class_regex, $SCHOOL_YEAR, $NOW;

	if (preg_match($_class_regex, $class, $matches)) {
		$class_num = $matches[1];

		// Autumn, Winter, Spring
		$holidays = $db->getAll("SELECT `start`, `end` FROM `holidays` LIMIT 3");

		if ($class_num == 8 || $class_num == 9) {

			if ($NOW <= new DateTime($holidays[0]['end'])) {
				return array(
					$TRUE_SCHOOL_YEAR_BEGIN,
					new DateTime($holidays[0]['start'])
				);

			} else if ($NOW <= new DateTime($holidays[1]['end'])) {
				return array(
					new DateTime($holidays[0]['end'] . ' tomorrow'),
					new DateTime($holidays[1]['start'])
				);

			} else if ($NOW <= new DateTime($holidays[2]['end'])) {
				return array(
					new DateTime($holidays[1]['end'] . ' tomorrow'),
					new DateTime($holidays[2]['start'])
				);

			} else {
				return array(
					new DateTime($holidays[2]['end'] . ' tomorrow'),
					$TRUE_SCHOOL_YEAR_END
				);
			}

		} else if ($class_num == 10 || $class_num == 11) {

			if ($NOW <= new DateTime($holidays[1]['end'])) {
				return array(
					$TRUE_SCHOOL_YEAR_BEGIN,
					new DateTime($holidays[1]['start'])
				);

			} else {
				return array(
					new DateTime($holidays[1]['end'] . ' tomorrow'),
					$TRUE_SCHOOL_YEAR_END
				);
			}
		}
	}

	return array($TRUE_SCHOOL_YEAR_BEGIN, $TRUE_SCHOOL_YEAR_END);
}


// ========================================== DATETIME ==========================================

function get_weekday($datetime) {
	return ($datetime->format('w') + 6) % 7;
}

function day_word_case($count) {
	if ($count == '1') return 'день';
	if ($count == '2') return 'дня';
	if ($count == '3') return 'дня';
	if ($count == '4') return 'дня';
	if (substr($count, -2) == '11') return 'дней';
	if (substr($count, -2) == '12') return 'дней';
	if (substr($count, -2) == '13') return 'дней';
	if (substr($count, -2) == '14') return 'дней';
	if (substr($count, -2) == '01') return 'день';
	if (substr($count, -1) == '2') return 'дня';
	if (substr($count, -1) == '3') return 'дня';
	if (substr($count, -1) == '4') return 'дня';
	return 'дней';
}

function format_days_diff($num, $upper=false) {
	if ($num == -2) return $upper ? 'Позавчера': 'позавчера';
	if ($num == -1) return $upper ? 'Вчера' : 'вчера';
	if ($num == 0) return $upper ? 'Сегодня' : 'сегодня';
	if ($num == 1) return $upper ? 'Завтра' : 'завтра';
	if ($num == 1) return $upper ? 'Послезавтра' : 'послезавтра';

	if ($num < 0) return -$num . ' ' . day_word_case($num) . ' назад';

	return ($upper ? 'Через ' : 'через ') . $num . ' ' . day_word_case($num);
}


// ============================================ LOG ============================================

function telegram_log($message, $force=true) {
	global $UI_ERROR, $config, $telegram;

	if (!$message) {
		// if ($force) $UI_ERROR = 'Please contact administrator with the following message: "Empty log message"';
		$message = 'Empty log message';
		return;
	}

	$message = mb_convert_encoding($message, 'utf-8');

	$telegram->sendMessage(array(
		'chat_id' => $config['telegram_chatid'],
		'text' => "=== NetSchool PTHS website ===\n{$message}"
	));
}

function telegram_log_error_handler($errno, $errstr, $errfile, $errline) {

	if (!(error_reporting() & $errno)) {
		// Этот код ошибки не включён в error_reporting,
		// так что пусть обрабатываются стандартным обработчиком ошибок PHP
		return false;
	}

	// может потребоваться экранирование $errstr:
	$errstr = htmlspecialchars($errstr);

	switch ($errno) {
		case E_USER_ERROR:
			telegram_log(
				"User Exception: [$errno] $errstr\n" .
				"On line $errline in file $errfile"
			);
			exit(1);

		case E_USER_WARNING:
			telegram_log(
				"User Warning: [$errno] $errstr\n" .
				"On line $errline in file $errfile"
			);
			break;

		case E_USER_NOTICE:
			telegram_log(
				"User Notice: [$errno] $errstr\n" .
				"On line $errline in file $errfile"
			);
			break;

		default:
			telegram_log(
				"Unknown error type: [$errno] $errstr\n" .
				"On line $errline in file $errfile"
			);
			break;
	}

	/* Не запускаем внутренний обработчик ошибок PHP */
	return true;
}

set_error_handler('telegram_log_error_handler');

function between($val, $a, $b){
	$min = min($a, $b);
	$max = max($a, $b);
	if($val >= $min && $val <= $max) return true;
	return false;
}



// LANDING PAGE

if (!isset($LANDING_PAGE) || !$LANDING_PAGE) {
	redirect('/land');
	exit;
}


?>
