<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST) || !isset($_POST['username']) || !isset($_POST['password'])) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

header('Content-Type: application/json');

if ($AUTHORIZED) exit('success');

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if (strlen($username) < 6) exit(json_encode(array('username', 'Логин слишком короткий')));
if (strlen($password) < 4) exit(json_encode(array('password', 'Пароль слишком короткий')));

if (is_null($db) || !$db) {
	try {
		$db = dbConnect();
	} catch (Exception $e) {
		// print_r($e);
		telegram_log("Database connection failed\n\n" . $e->getMessage());
		exit(json_encode(array('message', 'Database connection failed')));
	}
}

try {
	$data = $db->getAll('SELECT `id`, `username`, `password`, `last_update` FROM `users` WHERE `username` = ?s LIMIT ?i', $username, 2);
} catch (Exception $e) {
	// print_r($e);
	telegram_log("Database request failed\n\n" . $e->getMessage());
	exit(json_encode(array('message', 'Database request failed')));
}

switch (count($data)) {
	case 0:
		try {
			$data = $db->query('INSERT INTO `users` (`username`, `password`) VALUES (?s, ?s)', $username, $password);
		} catch (Exception $e) {
			// print_r($e);
			telegram_log("Database request failed\n\n" . $e->getMessage());
			exit(json_encode(array('message', 'Database request failed')));
		}

		telegram_log("User added\nUsername: {$username}");

		exit(json_encode(array('message', 'Указанный логин был добавлен в очередь на обработку.<br>Если вы указали верные данные, вход будет осуществлён автоматически через несколько секунд.')));

	case 1:
		$person = $data[0];

		if ($person['password'] != $password) exit(json_encode(array('password', 'Неверный пароль')));

		if (is_null($person['last_update'])) exit(json_encode(array('message', 'Ваша учётная запись ещё не готова.<br>Пожалуйста, подождите.')));

		if (!setcookie('session', person_hash($person), time() + 60 * 60 * 24 * 365 * 100, '/; samesite=Lax', $httponly=false)) exit(json_encode(array('message', 'Failed to set cookie')));

		exit('success');

	default:
		exit(json_encode(array('message', 'Please, contact administrator (too many rows)')));
}
?>
