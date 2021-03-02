<?php
// Creates $AUTHORIZED flag, $db and $person variables

$AUTHORIZED = isset($_COOKIE['session']);

$db = null;

if ($AUTHORIZED) {
	try {
		$db = dbConnect();
	} catch (Exception $e) {
		telegram_log("Database connection failed\n\n" . $e->getMessage());
		$AUTHORIZED = false;
		$UI_ERROR = 'Database connection failed';
	}
}

if ($AUTHORIZED) {

	try {
		$data = $db->getAll('SELECT * FROM `users` WHERE MD5(CONCAT(`id`, `username`, `password`)) = ?s LIMIT ?i', $_COOKIE['session'], 2);
	} catch (Exception $e) {
		// print_r($e);
		telegram_log("Database request failed\n\n" . $e->getMessage());
		$UI_ERROR = 'Database request failed (auth)';
	}

	if (count($data) > 1) {
		telegram_log("Too many rows");
		$UI_ERROR = 'Please, contact administrator (too many rows)';
	}

	if (count($data) == 0) {
		telegram_log("Session not found");
		logout();
		redirect();
		exit;
	}

	$person = $data[0];
}

?>