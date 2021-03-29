<?php
// Creates $AUTHORIZED flag, $db and $person variables

$AUTHORIZED = isset($_COOKIE['session']);

$db = null;
$person = null;

if ($AUTHORIZED) {
	try {
		$db = dbConnect();
	} catch (Exception $e) {
		// print_r($e);
		$AUTHORIZED = false;
		telegram_log("Database connection failed\n\n" . $e->getMessage());
		$UI_ERROR = 'Database connection failed';
	}
}

if ($AUTHORIZED) {

	try {
		$data = $db->getAll('SELECT 1 FROM `users` WHERE MD5(CONCAT(`id`, `username`, `password`)) = ?s LIMIT ?i', $_COOKIE['session'], 2);
	} catch (Exception $e) {
		// print_r($e);
		$AUTHORIZED = false;
		telegram_log("Database request failed\n\n" . $e->getMessage());
		$UI_ERROR = 'Database request failed (auth)';
	}

	if (count($data) > 1) {
		$AUTHORIZED = false;
		telegram_log("Too many rows");
		$UI_ERROR = 'Please, contact administrator (too many rows)';
	}

	if (count($data) == 0) {
		$AUTHORIZED = false;
		telegram_log("Session not found");
		logout();
		redirect();
		exit;
	}
}

function get_person() {
	global $db, $AUTHORIZED, $person;

	if ($AUTHORIZED) {

		try {
			$data = $db->getAll('SELECT * FROM `users` WHERE MD5(CONCAT(`id`, `username`, `password`)) = ?s LIMIT ?i', $_COOKIE['session'], 2);
		} catch (Exception $e) {
			// print_r($e);
			$AUTHORIZED = false;
			telegram_log("Database request failed\n\n" . $e->getMessage());
			$UI_ERROR = 'Database request failed (auth)';
		}

		if (count($data) > 1) {
			$AUTHORIZED = false;
			telegram_log("Too many rows");
			$UI_ERROR = 'Please, contact administrator (too many rows)';
		}

		if (count($data) == 0) {
			$AUTHORIZED = false;
			telegram_log("Session not found");
			logout();
			redirect();
			exit;
		}

		$person = $data[0];
	}
}
?>
