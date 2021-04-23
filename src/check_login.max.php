<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

// Result is:
// true - account ready for usage
// false - account not ready
// {message} - error message or account_does_not_exist message (was probably deleted due to an incorrect username/password) 


if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST) || !isset($_POST) || !isset($_POST['username'])) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

if ($AUTHORIZED) exit('success');

$username = trim($_POST['username']);

if (is_null($db) || !$db) {
	try {
		$db = dbConnect();
	} catch (Exception $e) {
		// print_r($e);
		telegram_log("Database connection failed\n\n" . $e->getMessage());
		exit('Database connection failed');
	}
}

try {
	$data = $db->getAll('SELECT `id`, `username`, `password`, `last_update` FROM `users` WHERE `username` = ?s LIMIT ?i', $username, 2);
} catch (Exception $e) {
	// print_r($e);
	telegram_log("Database request failed\n\n" . $e->getMessage());
	exit('Database request failed');
}

switch (count($data)) {
	case 0:
		exit('Логин/пароль неверны или достигнут лимит 3 попытки в минуту. Пожалуйста, проверьте правильность данных и попробуйте снова.');

	case 1:
		$person = $data[0];

		if (!is_null($person['last_update'])) {
			if (!setcookie('session', person_hash($person), time() + 60 * 60 * 24 * 365 * 100, '/; samesite=Lax', $httponly=false))
				exit('Failed to set cookie');
			
			exit('true');
		}
	
		exit('false');

	default:
		exit('Please, contact administrator (too many rows)');
}
?>
