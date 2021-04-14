<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST) || !isset($_POST)) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	redirect();
	exit;
}

header('Content-Type: application/json');

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

if (count($data) > 1) exit('Please, contact administrator (too many rows)');

if (count($data) == 0) {
	exit('Логин/пароль не верны или достигнут лимит в 3 попытки в минуту. Пожалуйста, проверьте правильность данных и попробуйте снова.');
}

// Then count($data) == 1:

$person = $data[0];

if (!is_null($person['last_update'])) {
	if (!setcookie('session', person_hash($person), time() + 60 * 60 * 24 * 365 * 100, '/; samesite=Lax', $httponly=false)) exit('Failed to set cookie');
	echo 'success';
}
?>
